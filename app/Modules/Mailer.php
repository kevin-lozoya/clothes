<?php
namespace App\Modules;

use App\Modules\Log;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer {

  public static function send(string $address, string $subject, string $body) {
    $status = false;
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = getenv('SMTP_DEBUG');              // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = getenv('SMTP_HOST');                    // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = getenv('SMTP_USER');                // SMTP username
        $mail->Password = getenv('SMTP_PASS');                // SMTP password
        $mail->SMTPSecure = getenv('SMTP_SECURE');            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = getenv('SMTP_PORT');                    // TCP port to connect to

        //Recipients
        $mail->setFrom('no-reply@clothes.es', 'Clothes');
        // $mail->addAddress('joe@example.net', 'Joe User');  // Add a recipient
        $mail->addAddress($address);                          // Name is optional
        // $mail->addReplyTo('info@example.com', 'Information');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');      // Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg'); // Optional name

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        $status = true;
    } catch (Exception $e) {
      Log::logError($mail->ErrorInfo);
    }

    return $status;
  }

}
?>