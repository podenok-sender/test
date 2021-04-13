<?php
include (config.php)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
function newmail($subject, $message, $file){
try {
    	//Server settings
    	$mail->setLanguage('ru', '../vendor/phpmailer/phpmailer/language/'); // Перевод на русский язык
   
    	//Enable SMTP debugging
    	// 0 = off (for production use)
    	// 1 = client messages
    	// 2 = client and server messages
    	$mail->SMTPDebug = 0;                                 // Enable verbose debug output

    	$mail->isSMTP();                                      // Set mailer to use SMTP
   
    	$mail->SMTPAuth = true;                               // Enable SMTP authentication

    	$mail->SMTPSecure = 'ssl';                          // secure transfer enabled REQUIRED for Gmail
    	$mail->Port = 465;                                  // TCP port to connect to
   	//$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    	//$mail->Port = 587;                                    // TCP port to connect to
   
    	$mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
    	$mail->Username = $from;               // SMTP username
   	$mail->Password = $password;                         // SMTP password
	$mail->CharSet = 'UTF-8';	
    	//Recipients
    	$mail->setFrom($from);
   	$mail->ClearAddresses(); 
   	$mail->addAddress($to);              // Name is optional	 

//Content
   	$mail->isHTML(false);                 // Set email format to HTML
    	$mail->Subject = $subject;
    	$mail->Body    = $message;
    	//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
      	$mail->AddAttachment($file);
   	return $mail->send(); 
} catch (Exception $e) {


}

}




