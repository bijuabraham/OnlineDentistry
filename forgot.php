<?php
require_once 'od_utils.php';
od_header();
if ($_POST['Submit']=='Send') {
    $host = $_SERVER['HTTP_HOST'];
    db_connect("CCDB");
    $rs_search = db_fetch_all("CCDB", "select user_email from sessions where user_email='$_POST[email]'");
    $user_count = db_num_rows($rs_search);

    if ($user_count != 0) {
        $newpwd = rand(1000,9999);
        $host = $_SERVER['HTTP_HOST'];
        $newmd5pwd = md5($newpwd);
        db_exec("CCDB", "UPDATE sessions set user_pwd='$newmd5pwd' where user_email='$_POST[email]'");
$message =
"You have requested new login details from $host. Here are the login details...\n\n
User Name: $_POST[email] \n
Password: $newpwd \n
____________________________________________
*** LOGIN ***** \n
To Login: http://$host/portal/login.php \n\n

Click on Settings to change your password anytime.
_____________________________________________
Thank you. This is an automated response. PLEASE DO NOT REPLY.
";

        mail($_POST['email'], "New Login Details", $message,
        "From: \"Online Dentistry\" <admin@$host>\r\n" .
         "X-Mailer: PHP/" . phpversion());

        die("Thank you. New Login details has been sent to your email address");
    } else die("Account with given email does not exist");

    db_close("CCDB");
}
?>
<h3>Forgot Password</h3>
<p>Please enter your email address and the new password will be sent.</p>
<table width="50%" border="0" cellpadding="1" cellspacing="0">
  <tr>
    <td>
      <form name="form1" method="post" action="">
        <p><br>
          <strong>Email:</strong>
          <input name="email" type="text" id="email">
          <input type="submit" name="Submit" value="Send">
        </p>
      </form></td>
  </tr>
</table>
<p>&nbsp;</p>
<?php od_footer(); ?>

