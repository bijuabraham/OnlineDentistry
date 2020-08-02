<?php
ini_set("display_errors","1");
ERROR_REPORTING(E_ALL);
    require_once 'od_utils.php';
    session_start();
    od_authenticate();
    od_header();
    od_top_menu();
?>
<?php
    $user = $_SESSION['user'];
    $envelope = $_SESSION['envelope'];
    $linked = $_SESSION['linked'];
    if (od_user_check($envelope, $linked, $user)) {
        db_connect("CCDB");
        $sqlquery = "select
    full_name as Name ,
    user_email as Email,
    envelope as Envelope
from
    sessions
where
    envelope=$envelope";

    $result = db_fetch_all("CCDB", $sqlquery);
    $num=db_num_rows($result);
    $i=0;
    while ($i < $num) {
        $name=db_result($result,$i,"Name");
        $email=db_result($result,$i,"Email");
        if ($i == 0) {
            print "<h2>My Information</h2>";
            print "<table class='special' >";
            print "<tr><td ><B>Name</B></td><td>$name</td></tr>";
            print "<tr><td ><B>Email</B></td><td>$email</td></tr>";
            print "<tr><td ><B>Admin ID</B></td><td>$envelope</td></tr>";
        }
        $i++;
    }
    print "</table>";
    print "<BR>";
    db_close("CCDB");
    print "<p>If any of the above information is incorrect, please email <a href=\"mailto:admin@keralaonlineedu.com\">admin@keralaonlineedu.com</a> with the details. </p>";
}

od_footer();
?>
