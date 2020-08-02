<?php
    require_once 'mtcsf_utils.php';
    session_start();
    mtcsf_authenticate();
    mtcsf_header();
    mtcsf_top_menu();
    $envelope = $_POST['envelope'] ?? "";
    $newlink = $_POST['newlink'] ?? "";
    $newenvelope = $_POST['newenvelope'] ?? "";
    $newname = $_POST['newname'] ?? "";
    $email = $_POST['email'] ?? "";
    $user = $_SESSION['user'];

?>
<h2>Accounts - Database Upload </h2>

<?php if (user_admin($user)) {
    db_connect("CCDB");
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
    mtcsf_footer();
//END OF MAIN
function file_upload_form()
{
?>
    <table>
    <form enctype="multipart/form-data" method="POST">
    <input type ="hidden" name="MAX_FILE_SIZE" value = "1000000"/>
    <tr>
    <td>
        Choose a file to upload:
    </td>
    <td>
        <input name="file" type="file"/>
    </td>
    </tr>
    <tr>
    <td>
        Choose the table to import into:
    </td>
    <td>
        <select name="table_selected">
        <option value="contributions">Income</option>
        <option value="contributions-last">Income Last Year</option>
        <option value="dues">Dues</option>
    </select>
    </td>
    </tr>
    <tr><td colspan=2>
        <input type="submit" name="file_submit" value="upload"/>
    </td></tr>
    </form>
    </table>
    <p><A HREF="DuesUploadTemplate.csv">Dues upload template</A></P>
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
    $databasetable = $_POST['table_selected'];
    if ($databasetable === "contributions-last") {
        $last_year = date("Y") - 1;
        $databasetable = "contributions$last_year";
    }
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
    $db_query = "truncate $databasetable";
    print "Deleting all entries from $databasetable ...<br>";
    $result = db_exec("CCDB", $db_query);
    if (!$result) {
        print "Deleting  $databasetable Failed\n";
    }

    $db_query = "insert into $databasetable (envelope, name, fund_no, "
                . "description, amount) ";
    foreach($lines_array as $line) {

        $lines++;
        //if ($lines == 1) continue; //skip first line

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
    update_pending_table();
    $queryasofdate ="UPDATE asofdate SET Date=CURDATE() WHERE 1";
    $result = db_exec("CCDB", $queryasofdate);
    if (!$result) {
        print "UPDATE AS_OF_DATE Query failed\n";
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

function update_pending_table()
{

    $this_year = date("Y");
    $last_year = $this_year - 1;
    $past_year = $this_year - 2;


    $q0 = "truncate pending";
    $q1 = 'INSERT INTO pending(
           SELECT DISTINCT envelope, name, "4120",'
           . '"' .  "Contribution - $last_year Dues" . ' ", "NOT PAID" '
           . "FROM contributions$last_year "
           . "WHERE envelope NOT IN (
                                     SELECT DISTINCT envelope
                                     FROM contributions$past_year
                                     WHERE fund_no =4120
                                    )
                              )" ;

    $q2 = 'INSERT INTO pending(
           SELECT DISTINCT envelope, name, "4120", "Contribution (Monthly)",
           "NOT PAID" '
           . "FROM contributions$last_year
              WHERE envelope NOT IN (
                                     SELECT DISTINCT envelope
                                     FROM contributions
                                     WHERE fund_no =4120
                                    )
                             )" ;


    $q3 = 'INSERT INTO pending(
           SELECT DISTINCT envelope, name, "4400", "Suvisesha Nidhi (Deppi)",
           12 '
           . "FROM contributions$last_year
              WHERE envelope NOT IN (
                                     SELECT DISTINCT envelope
                                     FROM contributions
                                     WHERE fund_no =4400
                                    )
                              )" ;


    $q4 = 'INSERT INTO pending(
           SELECT DISTINCT envelope, name, "4130", "Diocesan Sunday Collection",
           20 '
           . "FROM contributions$last_year
              WHERE envelope NOT IN (
                                     SELECT DISTINCT envelope
                                     FROM contributions
                                     WHERE fund_no =4130
                                    )
                             )" ;

    $q5 = 'INSERT INTO pending(
    SELECT DISTINCT envelope, name, "4126", "Diocesan Development Fund", 30 '
    . "FROM contributions$last_year
    WHERE envelope NOT
    IN (
    SELECT DISTINCT envelope
    FROM contributions
    WHERE fund_no =4126
    ) )" ;

    $q6 = 'INSERT INTO pending(
    SELECT DISTINCT envelope, name, "4170", "After Service Refreshment", 250 '
    . "FROM contributions$last_year
    WHERE envelope NOT
    IN (
    SELECT DISTINCT envelope
    FROM contributions
    WHERE fund_no =4170
    ) )" ;

    $q7 = 'INSERT INTO pending(
    SELECT DISTINCT envelope, name, "4380", "One Day Income", "NOT PAID" '
    . "FROM contributions$last_year
    WHERE envelope NOT
    IN (
    SELECT DISTINCT envelope
    FROM contributions
    WHERE fund_no =4380
    ) )" ;

    $q8 = 'INSERT INTO pending(
    SELECT DISTINCT envelope, name, "4335", "Self Denial", "NOT PAID" '
    . "FROM contributions$last_year
    WHERE envelope NOT
    IN (
    SELECT DISTINCT envelope
    FROM contributions
    WHERE fund_no =4335
    ) )" ;

    $q9 = 'INSERT INTO pending(
    SELECT DISTINCT envelope, name, "4330", "Rasissa", "NOT PAID" '
    . "FROM contributions$last_year
    WHERE envelope NOT
    IN (
    SELECT DISTINCT envelope
    FROM contributions
    WHERE fund_no =4330
    ) )" ;

    $query_array = [$q0, $q1, $q2, $q3, $q4, $q5, $q6, $q7, $q8, $q9];
    foreach ($query_array as $query) {
        $result = db_exec("CCDB", $query);
        if (!$result) {
            print "Query Failed: $query\n";
        }
    }
}
?>
