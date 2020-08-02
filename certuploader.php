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
?>
<p>
<A href="admin.php">Administrator's Page</A>
</p>
<?php
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
    $fieldseparator = ",";
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
?>
