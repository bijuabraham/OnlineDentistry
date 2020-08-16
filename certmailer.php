<?php
    require_once 'mailer.php';
    require_once 'certgen.php';
    require_once 'od_utils.php';
    ob_start();
    od_authenticate();
    $message = $_POST['message'] ?? "";
    $title = $_POST['title'] ?? "";
    $by = $_POST['by'] ?? "";
    $on = $_POST['on'] ?? "";
    $template = $_POST['template'] ?? "";
    $counter_id = $_POST["counter_id"] ?? "0";
    $external = $_POST['external'] ?? FALSE;
    $attach = $_POST['attach'] ?? FALSE;
    $user = $_SESSION['user'];
    $server = $_SERVER['HTTP_HOST'];
    $host = preg_replace('/www./','',$server);
    $is_send = $_POST['Send'] ?? "";
    db_connect("CCDB");
    if ($message != "") {
        $emailquery = "select studentid as studentid, sendemail as sendemail, firstname as FirstName, lastname as LastName, certname as certname from mailinglist where ID = $counter_id";
        //print $sqlquery2;
        $resultemails = db_fetch_all("CCDB", $emailquery);
        #$num=db_num_rows($resultemails);
        $subject = "Online Dentistry - Certificate";
        //attachment 
        $recipient=db_result($resultemails,0,"sendemail");
        $certname=db_result($resultemails,0,"certname");
        $studentid=db_result($resultemails,0,"studentid");
        #function sendPHPMailer($toaddress, $toname, $subject, $attach, $message) {
        $sentMailResult = FALSE;
        $certgenerateresult = FALSE;
        #echo "<p>Params: $i : $studentid : $certname : $title : $by : $on : $external : $attach : $template </p>";
        if ($attach == "true") {
            #echo "Enter attach";
            $certgenerateresult = generateCertificate ($studentid, $certname, $title, $by, $on, $template);
            if ($certgenerateresult) {
                $sentMailResult = sendPHPMailer($studentid, $recipient, $certname, $subject, 1, $message, $external);
            }
        } else {
            #echo "Enter mail only";
            $sentMailResult = sendPHPMailer($studentid, $recipient, $certname, $subject, 0, $message, $external);
        }
        if($sentMailResult)  
        { 
            echo $studentid . ":" . $certname . "-SUCCESS<BR>"; 
        } 
        else
        { 
            echo($studentid . ":" . $certname . "-FAIL<BR>"); 
        }
        ob_flush();
    }
    db_close("CCDB");
    ob_flush();
    ob_end_flush();
?>

