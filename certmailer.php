<?php
    ini_set("display_errors","1");
    ERROR_REPORTING(E_ALL);
    require_once 'od_utils.php';
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
<h2>Email Notifier </h2>
<?php if (isset($_GET['msg'])) { echo "<div class=\"msg\"> $_GET[msg] </div>"; }
if ($is_send == '' || $is_send == 'Cancel') {
?>

<table width="65%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="e5ecf9" class="forumposts"><form name="form1" method="post" action="notifier.php">
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
        $emailquery = "select distinct(user_email) as sendemail from sessions";
        //print $sqlquery2;
        $resultemails = db_fetch_all("CCDB", $emailquery);
        $num=db_num_rows($resultemails);
        while ($i < $num) {
            $sendemail=db_result($resultemails,$i,"sendemail");
            print "$sendmail";
            //$sendemail="biju.abraham@gmail.com";
            //$sendemail="satish.david@gmail.com";
            mail($sendemail , "Mar Thoma Church Portal Notification", $message,
"From: \"Online Dentistry Portal\" <notifications@$host>\r\n" .
"X-Mailer: PHP/" . phpversion());
            $i++;
          }
      }
      db_close("CCDB");
      print "Emails sent Successfully. Do not refresh or resend from this Screen.";
}
if (($is_send == 'Review') && $message)
{
    ?>
<table width="65%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="e5ecf9" class="forumposts"><form name="form2" method="post" action="notifier.php" style="padding:5px;">
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
