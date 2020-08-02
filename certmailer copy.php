<?php
    ini_set("display_errors","1");
    ERROR_REPORTING(E_ALL);
    require_once 'fpdf.php';
    require_once 'od_utils.php';
    require_once 'phpmailer.php';
    session_start();
    od_authenticate();
    od_header();
    od_top_menu();
    $message = $_POST['message'] ?? "";
    $user = $_SESSION['user'];
    $server = $_SERVER['HTTP_HOST'];
    $host = preg_replace('/www./','',$server);
    $is_send = $_POST['Send'] ?? "";
?>
<h2>Certificate Mailer </h2>
<?php if (isset($_GET['msg'])) { echo "<div class=\"msg\"> $_GET[msg] </div>"; }
if ($is_send == '' || $is_send == 'Cancel') {
?>

<table width="65%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="e5ecf9" class="forumposts"><form name="form1" method="post" action="certmailer.php">
        <p><br>
          Type in the Message:<BR />
           <TEXTAREA name="message" cols=40 rows=10 id="message">
<?

print "$message";

?>
</TEXTAREA>
          <input type="submit" name="Send" value="Review">
        </p>
      </form></td>
  </tr>
</table>
<?php
}
if (($is_send == 'Send') && $message && user_admin($user)) {
    db_connect("CCDB");
    if ($message != "") {
        $emailquery = "select studentid as studentid, sendemail as sendemail, firstname as FirstName, lastname as LastName, certname as certcame from mailinglist";
        //print $sqlquery2;
        $resultemails = db_fetch_all("CCDB", $emailquery);
        $num=db_num_rows($resultemails);
        $i = 0;
        while ($i < $num) {
          #Convert
            $image = 'tmp_certificate.png';
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->Image($image,120,140,0,0);
            $doc = $pdf->Output(S);
            $encoded_content = chunk_split(base64_encode($doc)); 
            //attachment 
            $message .= "--$boundary\r\n"; 
            $message .="Content-Type: PDF; name=".$doc."\r\n"; 
            $message .="Content-Disposition: attachment; filename=".$doc."\r\n"; 
            $message .="Content-Transfer-Encoding: base64\r\n"; 
            $message .="X-Attachment-Id: ".rand(1000, 99999)."\r\n\r\n";  
            $message .= $encoded_content; // Attaching the encoded file with email 
            $recipient=db_result($resultemails,$i,"sendemail");
            #$sendemail->AddStringAttachment($doc, ‘cert.pdf’, ‘base64’, ‘application/pdf’);
            $header = "From: ". $SenderName . " <" . $recipient . ">\r\n"; //optional headerfields
            echo "Sending email to $certname at $sendemail ...";
            $sentMailResult = mail($recipient, $subject, $message, $header); //mail command
            if($sentMailResult )  
              { 
                echo "Success.<BR>"; 
                unlink($name); // delete the file after attachment sent. 
              } 
              else
              { 
                die("Failed"); 
              } 
            #mail($sendemail , "Online Dentistry - Course Certificate", $message,
#"From: \"Online Dentistry\" <certificates@$host>\r\n" .
#"X-Mailer: PHP/" . phpversion());
            $i++;
          }
      }
      db_close("CCDB");
      print "<BR>Emails sent Successfully. <font color=red>Do not refresh or resend from this Screen.</font>";
}

if (($is_send == 'Review') && $message)
{
    ?>
<table width="65%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="e5ecf9" class="forumposts"><form name="form2" method="post" action="certmailer.php" style="padding:5px;">
        <p><br>
          <B>Review the Message:</B><BR />
    <?php
    print "<BR>$message<BR><BR>";
    print "<input type=\"hidden\" name=\"message\" value=\"$message\" >";
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
od_footer();
?>
