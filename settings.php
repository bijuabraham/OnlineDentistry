<?php
session_start();

require_once 'od_utils.php';
od_authenticate();
od_header();
od_top_menu();

if ($_POST['Submit']=='Change') {
    db_connect("CCDB");
    $rsPwd = db_fetch_one("CCDB", "select user_pwd from sessions where user_email='$_SESSION[user]'");
    if (!$rsPwd) {
        die("Invalid Email");
    }
    $oldpwd = $rsPwd['user_pwd'];

    if ($oldpwd == md5($_POST['oldpwd'])) {
        $newpasswd = md5($_POST['newpwd']);

        db_exec("CCDB", "Update sessions SET user_pwd = '$newpasswd'
                    WHERE user_email = '$_SESSION[user]'
                    ") or die('DB update error');
      //headers are already output
      //header("Location: settings.php?msg=Password updated...");
      ?>
          <!--meta http-equiv="Refresh" content="1; url=settings.php?msg=Password updated..."-->
          <meta http-equiv="Refresh" content="1; url=login.php?msg=Password updated...">
      <?php
    } else {
      header("Location: settings.php?msg=ERROR: Password does not match...");
      ?>
          <meta http-equiv="Refresh" content="1; url=settings.php?msg=ERROR: Password does not match...">
      <?php
    }
    db_close("CCDB");
} else {
?>
<h1>Settings</h1>
<p>
  <?php if (isset($_GET['msg'])) { echo "<div class=\"msg\"> $_GET[msg] </div>"; } ?>
</p>
<h2>Change Password</h2>
<form action="settings.php" method="post" name="form3" id="form3">
  <p>Old Password
    <input name="oldpwd" type="password" id="oldpwd">
  </p>
  <p>New Password:
    <input name="newpwd" type="password" id="newpwd">
  </p>
  <p>
    <input name="Submit" type="submit" id="Submit" value="Change">
  </p>
</form>
<?php
}
od_simple_footer();
?>
