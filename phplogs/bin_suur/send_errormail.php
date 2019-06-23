#!/usr/bin/php -q
<?php

#include ('/home/webapp/phplogs/bin/conf/hosts_conf.php');
include ('/home/webapp/phplogs/bin/config.class.php');

#ini_set("memory_limit","2G");

#echo "\n Excludes: ".$config->config_values[exclude][vaartus3]."\n";

$config = config::getInstance();


send_mail($config);


function send_mail($config) {
        #/bin/echo "Hoiatus: esines OutOfMemory viga KREEDO LIVE nodeA logis (tasuks moelda kreedowli1 restardile)" | /bin/mail valve@sorts.emt.ee

	$Name = "XgLogs"; //senders name
	$email = "xglogs@emt.ee"; //senders e-mail adress
        $recipient = $config->config_values[errors][error_mail];
	$mail_body = sprintf($config->config_values[errors][process_message]); //mail body
        $subject = "Veateade XGLOGS masinast"; //subject
	
	$header = "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields

	mail($recipient, $subject, $mail_body, $header); //mail command :)
}

