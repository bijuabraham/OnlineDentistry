<?php
    ini_set("display_errors","1");
    ERROR_REPORTING(E_ALL);

    require_once 'od_utils.php';
    session_start();
    od_authenticate();
    od_header();
    od_top_menu();
    $envelope = $_POST['envelope'] ?? "";
    $newlink = $_POST['newlink'] ?? "";
    $newenvelope = $_POST['newenvelope'] ?? "";
    $newname = $_POST['newname'] ?? "";
    $email = $_POST['email'] ?? "";
    $user = $_SESSION['user'];
    $is_change = $_POST['Change'] ?? "";
?>
<h2>Account Administration Main</h2>
<?php if (isset($_GET['msg'])) { echo "<div class=\"msg\"> $_GET[msg] </div>"; } ?>

<table width="65%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="e5ecf9" class="forumposts"><form name="form1" method="post" action="admin.php" style="padding:5px;">
        <p><br>
          Envelope #:
          <input name="envelope" type="text" id="envelope">&nbsp;&nbsp;
          <input type="submit" name="Submit" value="Submit">
        </p>
      </form></td>
  </tr>
</table>
<?php

if (($is_change == 'Change') && $envelope && user_admin($user)) {
    //print "Admin User = $user";
    db_connect("CCDB");
    if ($newlink != "") {
        $sqlquery2 = 'update sessions set user_linked=:user_linked where envelope=:envelope and user_email=:user_email';
        //print $sqlquery2;
        $num_affected = db_pexecute("CCDB", $sqlquery2,
                                    array('user_linked' => $newlink,
                                          'envelope' => $envelope,
                                          'user_email' => $email,
                                   ));
        if ($num_affected == 0) {
            die("Error in query: $sqlquery2 with ($newlink, $envelope, $mail)");
        }
        if ($newlink == 1) {
            $message =
"Your Online Dentistry Admin Page is now approved.

Visit http://www.keralaonlineedu.com and click on \"ADMIN PORTAL\" to login to your account.

Thank you. This is an automated response. PLEASE DO NOT REPLY.

Provide your comments, suggestions and feedback to admin@keralaonlineedu.com
";

            mail($email , "Online Dentistry Account Linked", $message,
"From: \"Online Dentistry\" <certs@keralaonlineedu.com>\r\n" .
"X-Mailer: PHP/" . phpversion());
        }
    }
    if ($newname != "") {
        $sqlquery2 = 'update sessions set full_name=:full_name where envelope=:envelope and user_email=:user_email';
        $num_affected = db_pexecute("CCDB", $sqlquery2,
                                    array('full_name' => $newname,
                                          'envelope' => $envelope,
                                          'user_email' => $email,
                                   ));

        if ($num_affected == 0) {
            die("Error in query: $sqlquery2 with ($newname, $envelope, $mail)");
        }
    }
    if ($newenvelope != "") {
        $sqlquery2 = 'update sessions set envelope=:new_env where envelope=:envelope and user_email=:user_email';
        $num_affected = db_pexecute("CCDB", $sqlquery2,
                                    array('new_env' => $newenvelope,
                                         'envelope' => $envelope,
                                         'user_email' => $email,
                                   ));

        if ($num_affected == 0) {
            die("Error in query: $sqlquery2 with ($newenvelope, $envelope, $mail)");
        }
    }
    db_close("CCDB");
}
if (is_numeric($envelope) && user_admin($user)) {
    db_connect("CCDB");
    $sqlquery1 = 'select full_name,user_email,joined,user_activated,user_linked from sessions where envelope=' . $envelope;
    //print $sqlquery1;
    $result1=db_fetch_all("CCDB", $sqlquery1);
    $num=db_num_rows($result1);
    $i=0;
    while ($i < $num) {
        $name=db_result($result1,$i,"full_name");
        $linked=db_result($result1,$i,"user_linked");
        $email=db_result($result1,$i,"user_email");
        $activated=db_result($result1,$i,"user_activated");
        print "<form name=\"form$i\" method=\"post\" action=\"admin.php\" style=\"padding:5px;\">";
        print"<table border=1><tr><td><B>Name</B></td><td><B>Value</B></td><td><B>New Value</B></td></tr>";
        print "<tr><td>Name</td><td>$name</td><td><input name=\"newname\" type=\"text\" id=\"newname\"></td></tr>";
        print "<tr><td>Email</td><td>$email</td><td align=center>-</td></tr>";
        print "<tr><td>Envelope</td><td>$envelope</td><td><input name=\"newenvelope\" type=\"text\" id=\"newenvelope\"></td></tr>";
        print "<tr><td>Activated</td><td>$activated</td><td align=center>-</td></tr>";
        print "<tr><td>Linked</td><td>$linked</td><td><input name=\"newlink\" type=\"text\" id=\"newlink\"></td></tr>";
        print "</table>";
        print "<input type=\"submit\" name=\"Change\" value=\"Change\">";
        print "<input type=\"hidden\" name=\"envelope\" value=\"$envelope\"><input type=\"hidden\" name=\"email\" value=\"$email\"></form>";
        $i++;
      }
      print "BE VERY CAREFUL WHEN CHANGING ENVELOPE NUMBERS. THERE IS NO VALIDATION !!!!!";
      db_close("CCDB");
}
    od_footer();
?>
