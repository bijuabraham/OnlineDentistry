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
<h2>Certificate Template Upload</h2>

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
        Choose a template file to upload (PNG):
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
<?php
}

function process_file()
{
    echo "Uploading file " . $_FILES["file"]["name"];
    echo " of file type " . $_FILES["file"]["type"];
    echo " and size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
    #echo "Stored in: " . $_FILES["file"]["tmp_name"] . "<br/>";
    #echo "Error if any: " . $_FILES["file"]["error"] . "<br/>";
    $target_dir = "templates/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $tmpfile = $_FILES["file"]["tmp_name"];
    $targetfile = 
    $imageFileType = $_FILES["file"]["type"];   
    if($imageFileType != "image/jpg" && $imageFileType != "image/png" && $imageFileType != "imgae/jpeg"
    && $imageFileType != "image/gif" ) {
      echo "Error: Only JPG, JPEG, PNG & GIF files are allowed.";
      $uploadOk = 0;
    }
    $imageFileError = $_FILES["file"]["error"];
    if($imageFileError != 0) {
      echo "Error: File Upload error: $imaageFileError";
      $uploadOk = 0;
    }   
    $file = fopen($tmpfile,"r");
    if(!$file) {
        echo "Error: Opening image file failed\n";
        $uploadOk = 0;
    }
    $size = filesize($tmpfile);
    if(!$size) {
        echo "Error: File is empty.\n";
        $uploadOk = 0;
    }
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
      // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($tmpfile, $target_file)) {
            echo "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    fclose($file);
}
?>