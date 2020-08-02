<?php
    ini_set("display_errors","1");
    ERROR_REPORTING(E_ALL);
    require 'mailer.php';
    require 'certgen.php';
    require_once 'od_utils.php';
    od_authenticate();
    od_header();
    od_top_menu();
    $message = $_POST['message'] ?? "";
    $title = $_POST['title'] ?? "";
    $by = $_POST['by'] ?? "";
    $on = $_POST['on'] ?? "";
    $user = $_SESSION['user'];
    $server = $_SERVER['HTTP_HOST'];
    $host = preg_replace('/www./','',$server);
    $is_send = $_POST['Send'] ?? "";
?>
<h2>Certificate Mailer </h2>
<?php if (isset($_GET['msg'])) { echo "<div class=\"msg\"> $_GET[msg] </div>"; }
if ($is_send == '' || $is_send == 'Cancel') {
?>
<form method="post" action="certmailer.php">
<label for="Message">Email Message:</label><BR>
<textarea id="message" name="message" rows="10" cols="50">
Hello,<BR>
<BR>
Attached is your Online Dentistry Patricipation Certificate.<BR>
Thank you for attending the class.<BR>
<BR>
<B>Administratior</B><BR>
<I>Online Dentistry</I><BR>
<BR>
</textarea><BR><BR>
<label for="title">Course Title:</label><br>
<input type="text" name="title" id="title" value="Orthodonics Course"><br><br>
<label for="by">Instructor name:</label><br>
<input type="text" name="by" id="by" value="Dr. Binu Abraham"><br><br>
<label for="on">Course date:</label><br>
<input type="text" name="on" id="on" value="August 8, 2020"><br><br>
<input type="submit" name="Send" value="Review">
</form>
<?php
}
if (($is_send == 'Send') && $message && user_admin($user)) {
    foreach(glob("certs/*") as $f) {
      if( $f == "certs/*.p*") continue;
      unlink($f);
    }
    db_connect("CCDB");
    if ($message != "") {
        $emailquery = "select studentid as studentid, sendemail as sendemail, firstname as FirstName, lastname as LastName, certname as certname from mailinglist";
        //print $sqlquery2;
        $resultemails = db_fetch_all("CCDB", $emailquery);
        $num=db_num_rows($resultemails);
        $subject = "Online Dentistry - Certificate";
        $i = 0;
        while ($i < $num) {
            //attachment 
            $recipient=db_result($resultemails,$i,"sendemail");
            $certname=db_result($resultemails,$i,"certname");
            $studentid=db_result($resultemails,$i,"studentid");
            
            #function sendPHPMailer($toaddress, $toname, $subject, $attach, $message) {
            $sentMailResult = FALSE;
            echo $studentid . "," . $certname . "," . $title . "," . $by . "," . $on;
            if(generateCertificate ($studentid, $certname, $title, $by, $on)) {
              $sentMailResult = sendPHPMailer($studentid, $recipient, $certname, $subject, 1, $message);
            }
            if($sentMailResult)  
              { 
                echo $studentid . ":" . $certname . " - Success<BR>"; 
              } 
              else
              { 
                die($studentid . ":" . $certname . " - Failed<BR>"); 
              } 
            $i++;
          }
      }
      db_close("CCDB");
      print "<BR>Emails sent Successfully. <BR><font color=red>Do not refresh or resend from this Screen.</font>";
}

if (($is_send == 'Review') && $message)
{
  db_connect("CCDB");
    ?>
<table width="65%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="e5ecf9" class="forumposts"><form name="form2" method="post" action="certmailer.php" style="padding:5px;">
        <p><br>
          <B>Review the Message:</B><BR />
    <?php
    print "<BR>$message<BR>";
    print "<BR><B>Title:</B> $title<";
    print "<BR><B>By:</B> $by";
    print "<BR><B>On:</B> $on<BR>";
    print "<input type=\"hidden\" name=\"message\" value=\"$message\" >";
    print "<input type=\"hidden\" name=\"title\" value=\"$title\" >";
    print "<input type=\"hidden\" name=\"by\" value=\"$by\" >";
    print "<input type=\"hidden\" name=\"on\" value=\"$on\" >";
    $queryasofdate ="select date from AS_OF_DATE";
    $result = db_fetch_all("CCDB", $queryasofdate);
    $lastuploaddate = db_result($result,0,"date");
    if (!$lastuploaddate) {
        echo "AS_OF_DATE Query failed<BR>\n";
    } else {
        echo "Last upload date of Participants: $lastuploaddate <BR>\n";
    }
    $mailinglistquery ="select count(*) as count from mailinglist";
    $result = db_fetch_all("CCDB", $mailinglistquery);
    $participantcount = db_result($result,0,"count");
    if (!$participantcount) {
      echo "No Participant List found or Query failed.<BR>\n";
    } else {
      echo "Total Participants: $participantcount <BR>\n";
  }
    ?>
          <input type="submit" name="Send" value="Cancel">&nbsp;&nbsp;
          <input type="submit" name="Send" value="Send">
        </p>
      </form></td>
  </tr>
</table>
    <?php
}
?>
<p>
<A href="admin.php">Administrator's Page</A><BR />
</p>
<?php
db_close("CCDB");
od_footer();
?>
