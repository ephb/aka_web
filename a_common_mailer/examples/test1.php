<?php

include_once('../class.phpmailer.php');

$mail    = new PHPMailer();

$body    = $mail->getFile('contents.html');

$body    = preg_replace("[\\\\]",'',$body);

$mail->From     = "www-data@ikt.uni-hannover.de";
$mail->FromName = "IKT Hannover";

$mail->Subject = "PHPMailer Test Subject";

$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

$mail->MsgHTML($body);



#$mail->AddAddress("ikt_test@gmx.de", "Kolja Windeler");
#$mail->AddAddress("kkoolljjaa@googlemail.com", "Kolja Windeler");
$mail->AddAddress("jkw@stud.uni-hannover.de", "Kolja Windeler");

if(!$mail->Send()) {
  echo 'Failed to send mail';
} else {
  echo 'Mail sent';
}


?>
