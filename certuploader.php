<?php
    require_once 'od_utils.php';
    ini_set("display_errors","1");
    ERROR_REPORTING(E_ALL);
    session_start();
    od_authenticate();
    od_header();
    od_top_menu();
    $user = $_SESSION['user'];
    $server = $_SERVER['HTTP_HOST'];
    $host = preg_replace('/www./','',$server);

?>
<h2>Participants List - Database Upload </h2>

<?php if (user_admin($user)) {
    if (isset($_POST['file_submit'])) {
        process_file();
    } else {
        file_upload_form();
    }
}
od_footer();
//END OF MAIN
function file_upload_form()
{
    ?>
    <table width="65%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td bgcolor="e5ecf9" class="forumposts">
        <form enctype="multipart/form-data" method="POST">
        <input type ="hidden" name="MAX_FILE_SIZE" value = "1000000"/>
            Choose a file to upload:
        </td>
        <td>
            <input name="file" type="file"/>
        </td>
        </tr>
        <tr><td colspan=2>
            <input type="submit" name="file_submit" value="upload"/>
        </td></tr>
        </form>
    </table>
        <p><A HREF="data/participantslist.csv">Mailing List upload template</A></P>

<?php
}

function process_file()
{
    echo "Upload: " . $_FILES["file"]["name"] . "<br />";
    echo "Type: " . $_FILES["file"]["type"] . "<br />";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
    echo "Stored in: " . $_FILES["file"]["tmp_name"] . "<br/>";
    echo "Error if any: " . $_FILES["file"]["error"] . "<br/>";
    $addauto = 0;
    $fieldseparator = "|";
    $lineseparator = "/\r|\n/";
    $csvfile = $_FILES["file"]["tmp_name"];
    $file = fopen($csvfile,"r");

    if(!$file) {
        echo "Error opening data file.\n";
        exit;
    }

    $size = filesize($csvfile);

    if(!$size) {
        echo "File is empty.\n";
        exit;
    }

    $csvcontent = fread($file,$size);

    fclose($file);

    $lines = 0;
    $queries = "";
    $linearray = array();
    /* first line has the database columns */
    $lines_array = preg_split($lineseparator,$csvcontent, -1, PREG_SPLIT_NO_EMPTY);
    //db_cols = massage_line($lines_array[0], $fieldseparator);
    //print "Database $databasetable has columns:\n<br>";
    //print_r($db_cols);

    // First delete all data from the existing table
    db_connect("CCDB");
    error_log ("Deleting all rows in mailinglist",0);
    $databasetable = "mailinglist";
    $db_query = "truncate $databasetable";
    print "Deleting all entries from $databasetable ...<br>";
    $result = db_exec("CCDB", $db_query);
    if (!$result) {
        print "Deleting  $databasetable Failed\n";
    }

    $db_query = "insert into $databasetable (studentid, sendemail, firstname, lastname, certname) ";
    foreach($lines_array as $line) {

        $lines++;
        if ($lines == 1) continue; //skip first line

        $linearray = massage_line($line, $fieldseparator);

        $linemysql = implode("','",$linearray);

        if ($linemysql == "") continue;

        if($addauto)
            $query = $db_query . "values('','$linemysql');";
        else
            $query = $db_query . "values('$linemysql');";

        $queries .= $query . "\n";

        //print "The query is $query<br>";

        $result = db_exec("CCDB", $query);
        if (!$result) {
            print "Query failed\n<br>";
            print "The query at line $lines is $query<br>";
            exit;
        }
    }
    $queryasofdate ="UPDATE `AS_OF_DATE` SET `Date`=CURRENT_TIMESTAMP WHERE 1";
    $result = db_exec("CCDB", $queryasofdate);
    if (!$result) {
        print "UPDATE AS_OF_DATE Query failed\n";
    } else {
        print "Successfully Uploaded: $databasetable ; Updated Last Update date...<br>";
    }
    assign_studentid();
    db_close("CCDB");

    //print "The query is $queries<br>";
}

function massage_line($line, $fieldseparator)
{
        $line = trim($line," \t");

        $line = str_replace("\r","",$line);

        // replace quoted commas with semi-colon
        $pattern = '/"([A-Za-z\&0-9 ]+),([A-Za-z\&0-9 ]+)"/';
        $replacement = '"$1;$2"';
        $new_line = preg_replace($pattern, $replacement, $line);
        if ($new_line != "") {
            $line = $new_line;
        }


        /************************************
        This line escapes the special character. remove it if entries are already escaped in the csv file
        ************************************/
        $line = str_replace("'","\'",$line);
        /*************************************/
        /************************************
         Remove the double-quotes character
         *************************************/
        $line = str_replace('"','',$line);

        return explode($fieldseparator,$line);
}

function assign_studentid (){
    $query1 = "select sendemail as sendemail, firstname as FirstName, lastname as LastName, certname as certname from mailinglist";
    $resultemails = db_fetch_all("CCDB", $query1);
    $num=db_num_rows($resultemails);
    $i = 0;
    while ($i < $num) {
        $sendemail=db_result($resultemails,$i,"sendemail");
        $certname=db_result($resultemails,$i,"certname");
        $firstname=db_result($resultemails,$i,"FirstName");
        $lastname=db_result($resultemails,$i,"LastName");
        $query2 = "select studentid as studentid from studentlist where sendemail = '$sendemail'";
        $resultid = db_fetch_all("CCDB", $query2);
        $numids=db_num_rows($resultid);
        if ($numids == 1) {
            $studentid=db_result($resultid,0,"studentid"); 
            $query3 = "update mailinglist set studentid = $studentid where sendemail='$sendemail'";
            $result = db_exec("CCDB", $query3);
            if (!$result) {
                error_log ("Error executing $query3");
            }
        } else if ($numids == 0) {
            $query3 = "insert into studentlist (sendemail, firstname, lastname, certname, status, mailinglist) values ('$sendemail','$firstname','$lastname','$certname',1,1)";
            $result = db_exec("CCDB", $query3);
            if (!$result) {
                error_log ("Error executing $query3");
            }
            $query4 = "select studentid as studentid from studentlist where sendemail = '$sendemail'";
            $resultnewid = db_fetch_all("CCDB", $query4);
            if (!$resultnewid) {
                error_log ("Error executing $query4");
            }
            $studentid=db_result($resultnewid,0,"studentid");
            $query5 = "update mailinglist set studentid = $studentid where sendemail='$sendemail'";
            $resultupdate = db_exec("CCDB", $query5);
            if (!$resultupdate) {
                error_log ("Error executing $query5");
            }
        } else {
            error_log ("Error executing $query2");
        }
        $i++; 
    }
}
?>
