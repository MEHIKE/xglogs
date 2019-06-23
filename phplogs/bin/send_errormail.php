#!/usr/bin/php -q
<?php

require 'PHPMailerAutoload.php';

#include ('/home/webapp/phplogs/bin/conf/hosts_conf.php');
include ('/home/webapp/phplogs/bin/config.class.php');

#ini_set("memory_limit","2G");

#echo "\n Excludes: ".$config->config_values[exclude][vaartus3]."\n";

$config = config::getInstance();


send_mail($config);


function send_mail_bak($config) {
        #/bin/echo "Hoiatus: esines OutOfMemory viga KREEDO LIVE nodeA logis (tasuks moelda kreedowli1 restardile)" | /bin/mail valve@sorts.emt.ee

	$Name = "XgLogs"; //senders name
	$email = "xglogs@emt.ee"; //senders e-mail adress
        $recipient = $config->config_values[errors][error_mail];
	$mail_body = sprintf($config->config_values[errors][process_message]); //mail body
        $subject = "Veateade XGLOGS masinast"; //subject
	
	$header = "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields

	mail($recipient, $subject, $mail_body, $header); //mail command :)
}

function send_mail($config) {

        #$Name = "XgLogs"; //senders name
        #$email = "xglogs@emt.ee"; //senders e-mail adress
$mail = new PHPMailer;
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'telekom-smtp.estpak.ee';  // Specify main and backup SMTP servers
$mail->SMTPAuth = false;                               // Enable SMTP authentication
$mail->setFrom('xglogs@xglogs.emt.ee', 'XGLOGS Logide server');

        #$recipient = $config->config_values[errors][error_mail];
	$mail->addAddress($config->config_values[errors][error_mail]);     // Add a recipient
        #$mail_body = sprintf($config->config_values[errors][process_message]); //mail body
	$mail->Body    = sprintf($config->config_values[errors][process_message]); //mail body
        #$subject = "Veateade XGLOGS masinast"; //subject
	$mail->Subject = "Veateade XGLOGS masinast"; //subject

        #$header = "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields
        #mail($recipient, $subject, $mail_body, $header); //mail command :)

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}

}

