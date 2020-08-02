<?php
ini_set("display_errors","1");
ERROR_REPORTING(E_ALL);

session_start();
require 'od_utils.php';


if (isset($_POST['Submit']) && ($_POST['Submit']=='Login')) {
    db_connect("CCDB");
    /*
     * no need of escape with prepare query
     * $user_email = mysql_real_escape_string($_POST['email']);
     */
    $sql = "SELECT id,user_email,envelope,user_linked FROM sessions WHERE
                user_email = :user_email AND
                user_pwd = :user_pwd AND user_activated='1'";
    $sqllog = "INSERT INTO users(email, timestamp) VALUES (:email, NOW())";

    $stmt = db_prepare("CCDB", $sql);
    db_execute($stmt, array('user_email' => $_POST['email'],
                            'user_pwd' => md5($_POST['pwd'])));
    $result = db_stmt_fetchall($stmt);
    $num = db_num_rows($result);
    if ( $num != 0 ) {

        // A matching row was found - the user is authenticated.
        $user_id = db_result($result, 0,'id');
        $user_email = db_result($result, 0, 'user_email');
        //print_r($result);
        //print "User email is $user_email<br>";
        // this sets variables in the session
        $_SESSION['user'] = $user_email;
        $_SESSION['envelope'] = db_result($result, 0, 'envelope');
        $_SESSION['linked'] = db_result($result, 0, 'user_linked');
        db_pexecute("CCDB", $sqllog, array('email' => $user_email,));
        db_close("CCDB");
        if (isset($_GET['ret']) && !empty($_GET['ret']))
        {
            header("Location:$_GET[ret]");
        } else
        {
            $ref = $_SERVER['HTTP_REFERER'] ?? "";
            if ($ref) {
                header("Location:$ref");
            } else {
                header("Location:myinfo.php");
            }
        }
        //echo "SUCCESS with $user_email<br>";
        //header("location: " . $_SERVER['HTTP_REFERER']);
    } else {
        //echo "FAIL with $user_email<br>";
        db_close("CCDB");
        header("Location: login.php?msg=Invalid Login");
    }
} else {

    if (!isset($_SESSION['user'])) {
        od_header();
        if (isset($_GET['msg'])) { 
            echo "<div class=\"msg\"> $_GET[msg] </div>"; 
        }
    ?>
    <p>&nbsp;</p>
    <div id="centerbox">
    <div align="center"><font size="5"><strong>Online Dentistry Admin Portal
            </strong></font></div>
      <p>
        <form name="form1" method="post" action="">
            <p>&nbsp;</p>
            <p align="center">Your Email
              <input name="email" type="text" id="email">
            </p>
            <p align="center"> Password:
              <input name="pwd" type="password" id="pwd">
            </p>
            <p align="center">
              <input type="submit" name="Submit" value="Login">
            </p>
            <p align="center"><a href="register.php">Register</a> | <a href="forgot.php">Forgot</a></p>
          </form>
      </p>
      <br><br>
      <p align=center>To access the Admin portal please send an email to admin@keralaonlineedu.com</p>
    </div>

    <?php
        od_footer();
    } else {
        header("Location: myinfo.php");
    }
}
?>
