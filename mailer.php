<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require_once 'vendor/autoload.php';

function sendPHPMailer($studentid, $toaddress, $toname, $subject, $attach, $message, $external) {

    try {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      // Enable verbose debug output
        $mail->Debugoutput = 'error_log';
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.1and1.com';                    // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'onlinedentistry@keraladental.com';                     // SMTP username
        $mail->Password   = 'Binu1234';                               // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        
        //Recipients
        $mail->setFrom('no-reply@keralaonlineedu.com', $subject);
        if ($external) {
            $mail->addAddress($toaddress, $toname);     // Add a recipient
            $mail->addBCC('certificates@keralaonlineedu.com');               // Name is optional
        } else {
            $mail->addAddress('certificates@keralaonlineedu.com', 'Certificate');     // Add a recipient
        }
        #$mail->addCC('admin@keralaonlineedu.com');               // Name is optional
        $mail->addReplyTo('no-reply@keralaonlineedu.com', 'Do Not Reply');
        #$mail->addCC('cc@example.com');
        #$mail->addBCC('bcc@example.com');
        // Attachments
        if ($attach) {
            $mail->addAttachment("certs/" . $studentid . "_certificate.pdf");         // Add attachments
        }
        

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = $message;

        $mail->send();
        $mail->smtpClose();
        return TRUE;
    } catch (Exception $e) {
            error_log ("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return FALSE;
    }
}
