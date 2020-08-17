<?php
require_once 'od_utils.php';
ini_set("display_errors","0");
ERROR_REPORTING(E_ALL);
session_start();
od_authenticate();
od_header();
od_top_menu();
$message = $_POST['message'] ?? "";
$title = $_POST['title'] ?? "";
$by = $_POST['by'] ?? "";
$on = $_POST['on'] ?? "";
$template = $_POST['template'] ?? "";
$startcert = $_POST["startcert"] ?? "0";
$endcert = $_POST["endcert"] ?? "0";
$external = $_POST['external'] ?? FALSE;
$attach = $_POST['attach'] ?? FALSE;
$user = $_SESSION['user'];
$server = $_SERVER['HTTP_HOST'];
$host = preg_replace('/www./','',$server);
$is_send = $_POST['Send'] ?? "";
foreach(glob("certs/*") as $f) {
    if ($f == "certs/*.p*") continue;
    unlink($f);
}
?>
<link href="certform.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="certmailer.js"></script>
<!-- Form Outer -->
<div class="form_outer">
  <form name="form2" method="post" action="#" style="padding:5px;">
  <!-- Form Message -->
  <div class="form_message">
    <label for="Message">Email Message:</label><BR>
    <textarea id="message" name="message" rows="16" class = "textbox">
    Greeting Dr. {firstname},<BR>
    <BR>
    Thank You for participating in the Webinar {title} conducted by {by} on {on}. Attached please find the certificate of Participation. <BR>
    <BR>
    Disclaimer - This is an E-Certificate. The firm Online Dentistry is Not responsible for any manipulations by the candidate. Online Dentistry has a backup record of all the certificates issued.
    <BR><BR>
    Your student ID: {studentid}<BR>
    Name: {firstname} {lastname}<BR>
    Certificate Name: {certname}<BR>
    Email: {sendmail}<BR>
    <BR>
    <B>Team</B><BR>
    <I>Online Dentistry</I>
    </textarea>
    Reserved Strings: {studentid}, {firstname}, {lastname}, {certname}, {sendmail}<BR><BR>
  </div>
  <!-- Form Values -->
  <div class="form_values">
    <label for="title">Course Title: {title}</label><br>
    <input type="text" name="title" id="title" value="Orthodonics Course"><br><br>
    <label for="by">Instructor name: {by}</label><br>
    <input type="text" name="by" id="by" value="Dr. Binu Abraham"><br><br>
    <label for="on">Course date: {on}</label><br>
    <input type="text" name="on" id="on" value="August 8, 2020"><br><br>
  </div>
  <!-- Form Parameters -->
  <div class="form_params">
    <label for="template">Certificate Template:</label><br>
    <select id="template">
    <option value="standard">Standard</option>
    <option value="guest">Guest</option>
    </select><br><br>
    <label for="startcert">Starting Certificate Line:</label><br>
    <input type="number" name="startcert" id="startcert" value="1"><br><br>
    <label for="endcert">Ending Certificate Line:</label><br>
    <input type="number" name="endcert" id="endcert" value="100"><br><br>
    <input type="checkbox" id="external" name="external" value="external" unchecked><label for="external"> SEND EXTERNAL EMAILS</label><BR>
    <input type="checkbox" id="attach" name="attach" value="attach" unchecked><label for="attach"> ATTACH CERTIFICATE</label><BR>
  </div>
  <input type="submit" id="submit" name="submit" value="Send" onclick="move()">
  <input type="reset" id="reset" name="reset" value="Clear" onclick="clearprevious()">
  </form>
</div>
<!-- Form Result -->
<div class="form_result">
  <p id = "successmessage"></p>
  <p id = "errormessage"></p>
</div>
<!-- Form Progress Bar -->
<div id="myProgress">
  <div id="myBar"></div>
</div>
<?php od_footer(); ?>
