<?php
session_start();

require_once 'od_utils.php';
od_header();

if ($_POST['Submit'] == 'Register') {
    db_connect("CCDB");
    if (! validEmail($_POST['email'])) {
        db_close("CCDB");
        die ("Incorrect email. Please enter valid email address..");
    }
    if (strlen($_POST['envelope']) < 3) {
        db_close("CCDB");
        die ("Incorrect or missing envelope. Please enter valid envelope..");
    }
    if (strcmp($_POST['pass1'],$_POST['pass2']) || empty($_POST['pass1']) ) {
        //die ("Password does not match");
        db_close("CCDB");
        die("ERROR: Password does not match or empty..");
    }
    if (strcmp(md5($_POST['user_code']),$_SESSION['ckey'])) {
        db_close("CCDB");
             die("Invalid code entered. Please enter the correct code as shown in the Image");
    }
    $rs_duplicates = db_fetch_all("CCDB", "select id from sessions where user_email='$_POST[email]'");
    $duplicates = db_num_rows($rs_duplicates);
    if ($duplicates > 0) {
        db_close("CCDB");
        die ("ERROR: User account already exists.");
        //header("Location: register.php?msg=ERROR: User account already exists..");
        //exit();
    }



    $md5pass = md5($_POST['pass2']);
    $activ_code = rand(1000,9999);
    $server = $_SERVER['HTTP_HOST'];
    $host = preg_replace('/www./','',$server);
    $stmt = db_prepare("CCDB", "INSERT INTO sessions
                  (`user_email`,`user_pwd`,`envelope`,`joined`,`activation_code`,`full_name`)
                  VALUES (:user_email, :user_pwd, :envelope, now(), :activation_code, :full_name)");
    $num_affected = db_execute($stmt, 
         array('user_email' => $_POST[email], 'user_pwd' => $md5pass,
               'envelope' => $_POST[envelope], 
               'activation_code' => $activ_code, 
               'full_name' => $_POST[full_name]));
    if ($num_affected == 0) {
        db_close("CCDB");
        die("Error in DB update. Please try again or email webadmin");
    }
    $message =
"Thank you for registering an account with $server Portal. Here are the login details...\n\n
User Email: $_POST[email] \n
Password: $_POST[pass2] \n

Click the activation link below to activate your account.
After activation, it will take 24-48 hours for verification.
____________________________________________
*** ACTIVATION LINK ***** \n
Activation Link: http://$server/portal/activate.php?usr=$_POST[email]&code=$activ_code \n\n
_____________________________________________
Thank you. This is an automated response. PLEASE DO NOT REPLY.
";

    mail($_POST['email'] , "Online Dentistry Admin Portal Login Activation", $message,
    "From: \"Online Dentistry\" <admin@$host>\r\n" .
     "X-Mailer: PHP/" . phpversion());
    $message =
"The following user is registered to Online Dentistry Admin Portal. Here are the login details...\n\n
User Email: $_POST[email] \n
Envelope #: $_POST[envelope] \n

Goto http://www.keralaonlineedu.com/portal/admin.php to approve and link.
_____________________________________________
Thank you. This is an automated response. PLEASE DO NOT REPLY.
";

    mail("admin@keralaonlineedu.com" , "Online Dentistry Admin Portal New User Registered", $message,
    "From: \"Online Dentistry\" <admin@$host>\r\n" .
     "X-Mailer: PHP/" . phpversion());
    unset($_SESSION['ckey']);
    echo("Registration Successful! An activation link has been sent to your email address. Please check email to activate.");

    db_close("CCDB");
    exit;
}

?>
<link href="styles.css" rel="stylesheet" type="text/css">
<?php if (isset($_GET['msg'])) { echo "<div class=\"msg\"> $_GET[msg] </div>"; } ?>
<p>&nbsp;</p>
<div class="floatdiv">
  <h2>Register Administrator Account</h2>
    <form name="form1" method="post" action="register.php" style="padding:5px;">
        <p><br>
          Name:
          <input name="full_name" type="text" id="full_name">
          Ex. John Doe</p>
        <p>Email:
          <input name="email" type="text" id="email">
          Ex. user@domain.com</p>
        <p>Password:
          <input name="pass1" type="password" id="pass1">
          Atleast 5 chars</p>
        <p>Retype Password:
          <input name="pass2" type="password" id="pass2">
        </p>
        <p>Administrator #:
          <input name="envelope" type="text" id="envelope">
        Send an email to <A href="mailto:admin@keralaonlineedu.com">admin@keralaonlineedu.com</A>, if you are not sure of your Administrator #</p>
        <p>Repeat the number shown:
          <input name="user_code" type="text" size="10">
          <img src="pngimg.php" align="middle">&nbsp; </p>
        <p>
          <input type="submit" name="Submit" value="Register">
        </p>
      </form>
</div>
<p class="clear"></p>
<?
od_simple_footer();

function validEmail($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if
(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}
?>
