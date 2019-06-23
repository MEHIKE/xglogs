#!/usr/bin/php -q
<?php

require 'PHPMailerAutoload.php';

include ('/home/webapp/phplogs/bin/conf/hosts_conf.php');
include ('/home/webapp/phplogs/bin/config.class.php');



$config = config::getInstance();
#print "Päevade arv confis= ".$config->config_values[others][days];
#print $config->config_values[paths][INCOMMING_PATH];
echo "\n\n***********************************************************************************************************************************************";
echo "Kopeerimise kontrolli scripti algus: ".date("d-M-Y G:i:s",strtotime("now"))."\n\n";
print "Inncoming path= ".$config->config_values[paths][incoming_path]."\n";
$algus=strtotime("now");

print "Excludes: ".$config->config_values[exclude][vaartus]."\n";
$str = "now - ".$config->config_values[others][days]." days";

#leiame eelmise tunni
$tund = "now - 2 hours";
$tund_time = strtotime($tund);
$eelmine_tund_str = date("Y-m-d-G",$tund_time);
echo "eelmine tund=$eelmine_tund_str \n";

$pp = "now - 2 days";
$pp_time = strtotime($pp);
$eelmine_paev_str = date("Y-m-d-G",$pp_time);
echo "eelmine paev=$eelmine_paev_str \n";

$tund = "now - 1 hours";
$tund_time = strtotime($tund);
$praegune_tund_str = date("Y-m-d-G",$tund_time);
echo "praegune tund=$praegune_tund_str \n";

$pp = "now - 1 days";
$pp_time = strtotime($pp);
$praegune_paev_str = date("Y-m-d-G",$pp_time);
echo "praegune paev=$praegune_paev_str \n";

#echo "\n $str \n";
#hetke tunni failid
$paev = strtotime($str);
#$paev = strtotime("now - 3 days");
$mydate = date("Y-m-d-G",$paev);
print "Viimane copytav aeg= $mydate \n \n";

#$excludes = $config->config_values[exclude][vaartus];
# kataloogi olemasolu kontroll
if (is_dir($config->config_values[paths][incoming_path])) {
 #echo "Kataloog on olemas: ".$config->config_values[paths][incoming_path]."\n";
} else {
 echo "Confis antud kataloog pole tegelikult kataloog: ".$config->config_values[paths][incoming_path]."\n";
}

# failide massiiv, kus kirjas koik incoming kataloogi failid
#$incoming_files = get_files_from_path($config->config_values[paths][incoming_path]);
$files = get_files_from_path($config->config_values[paths][processed_path]);

#var_dump($processed_files);'
#$files = array_merge($incoming_files, $processed_files);
#print_r($files);

echo "Läheb kopimise kontrolliks, eelmise tunni failid: \n";
$text='';
foreach($files as $yks => $filename ) {
	if (!is_dayfile($filename, $config)) {
		if (substr_count($filename, $eelmine_tund_str) > 0 ) {
		#if (!is_dayfile($filename, $config)) {
			$name = substr($filename, 0, strpos($filename, "."));
	        	#echo "filename $filename ja name= $name\n";
			#$ii=0;
			$otsing = FALSE;
			foreach($files as $kaks => $hetke_filename )
			{
				if (substr_count($hetke_filename, $name) > 0 ) {
					if (substr_count($hetke_filename, $praegune_tund_str) > 0 ) {
						#echo "ok= $hetke_filename \n";
						$otsing = TRUE;
						break;
					}
				}
				#$ii++;
				#echo "$ii, ";
				#echo "not OK \n";
			}
			if (!$otsing) {
				$text = $text."Oli olemas eelmise tunni logifail $filename aga pole kopeeritud uut! \n";
				#echo "text= $text";
			}
		#}
		}
	} else {
                if (substr_count($filename, $eelmine_paev_str) > 0 ) {
                #if (!is_dayfile($filename, $config)) {
                        $name = substr($filename, 0, strpos($filename, "."));
                        #echo "paeva filename $filename ja name= $name\n";
                        #$ii=0;
                        $otsing = FALSE;
                        foreach($files as $kaks => $hetke_filename )
                        {
                                if (substr_count($hetke_filename, $name) > 0 ) {
                                        if (substr_count($hetke_filename, $praegune_paev_str) > 0 ) {
                                                #echo "ok= $hetke_filename \n";
                                                $otsing = TRUE;
                                                break;
                                        }
                                }
                                #$ii++;
                                #echo "$ii, ";
                                #echo "not OK \n";
                        }
                        if (!$otsing) {
                                $text = $text."Oli olemas eelmise päeva logifail $filename aga pole kopeeritud uut! \n";
                                #echo "text= $text";
                        }
                #}
                }

	}
}
if ($text != '') {
	echo $text;
	send_mail($config, $text);
} else {
	echo "Kõik failid on kopeeritud! \n";
}
$lopp1 = strtotime("now");
$p_aeg1 = $lopp1 - $algus;
echo "\nKopeerimise kontrolliks kulus: ".tund_from_sek($p_aeg1)."tund ".min_from_sek($p_aeg1-tund_from_sek($p_aeg1)*3600)."min ".sek_from_sek($p_aeg1)."sek\n";
echo "\nKopeerimise kontrolli scripti lõpp: ".date("d-M-Y G:i:s",strtotime("now"))."\n\n";
$algus1 = $lopp1;

#logide tõstmine processed kataloogi, samal ajal igale reale kellaaega lisades, kui pole
echo "Kettal vaba ruumi: ".((int)(disk_free_space("/")/1024/1024/1024))."Gb\n";
echo "***********************************************************************************************************************************************\n\n";


function sek_from_sek($time) {
        if ($time < 60) {
                return $time;
        }
        else {
                do {
                        $time = $time - (int)($time / 60)*60;
                } while ($time > 60);
        }
        if ($time < 10) {
                #return " 0".$time."sek";
        }
        return $time;
}

function min_from_sek($time) {
        if ($time < 60) {
                return 0;
        }
        else {
                do {
                        $time = (int)($time / 60);
                } while ($time > 60);
        }
        if ($time == 60) {
                $time = 0;
        }
        if ($time < 10) {
        #       return " 0".$time."min";
        }
        return $time;
}

function tund_from_sek($time) {
        if ($time < 3600) {
                return 0;
        }
        else {
                do {
                        $time = ((int)($time / 3600)) ;
                } while ($time > 3600);
        }
        if ($time == 60) {
                $time = 0;
        }
        if ($time < 10) {
                #return " 0".$time."tundi";
        }
        return $time;
}

function get_files_from_path($path) {
	$open_path = opendir($path);
	$files[] = array();
	while (($file = readdir($open_path)) !== false) {
#        	if ( $file != '.' and $file != '..' and $file != 'processed') {
		if (is_file($path.'/'.$file)) {
                	$files[] = $file;
	        }
	}
	closedir($open_path);
	return $files;
}


function copy_logs($host, $host_values, $config, $paev, $files) {

	return;
}


function not_excluded($filename, $config) {
	$excluded = explode(',',$config->config_values[exclude][vaartus]);
	foreach ($excluded as $name) {
		if (substr_count($filename, $name) > 0 ) {
			return FALSE;
		}
	}
	return TRUE;
}

function add_prefix($host, $filename, $config) {
        $prefix = explode(',',$config->config_values[prefix][vaartus]);
        foreach ($prefix as $name) {
                if (substr_count($filename, $name) > 0 and substr_count($filename, $host) <= 0 ) {
                        return $host."-".$filename;
                }
        }
        return $filename;
}

function is_dayfile($filename, $config) {
        $sufix = explode(',',$config->config_values[sufix][vaartus]);
        foreach ($sufix as $name) {
                if (substr_count($filename, $name) > 0 ) {
                        return TRUE;
                }
        }
        return FALSE;
}

function to_old($filename, $paev) {
	$aeg = substr($filename, strpos($filename, ".log." )+5, 10);
#        echo "aeg= $aeg \n";
#2009-02-02
#0123456789
	$file_time = mktime(0,0,0,substr($aeg,5,2), substr($aeg, 8,2), substr($aeg, 0,4));

#	$mydate = date("Y-m-d-G", $file_time);
#	echo "filetime= $file_time ja time= $paev  mydtae= $mydate\n";
	if ($file_time > $paev) {
#		echo "failiaeg aeg on suurem\n";
		return FALSE;
	}
#	else {
		return TRUE;
#	}
}


function is_copied($filename, $files) {
	foreach ($files as $name) {
		if (substr_count($filename, $name) > 0 ) {
			return TRUE;
		}
	
#		echo "test= $name ja $filename \n";
	}
	return FALSE;
}


function nodesize( $node )
{
    if( !is_readable($node) ) return false;
    $blah = exec( "/usr/bin/du -sk $node" );
    return substr( $blah, 0, strpos($blah, 9) );
}


function send_mail_bak($config, $text) {
        echo "Saadan emaili = $text \n";
	#echo "Saadan emaili = $text" | /bin/mail rynno.ruul@emt.ee
$Name = "XgLogs"; //senders name
$email = "xglogs@emt.ee"; //senders e-mail adress
$recipient = $config->config_values[errors][warning_mail];
$mail_body = $text; //mail bo
$subject = "Veateade XGLOGS masinast, check.php"; //subject
$header = "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields

mail($recipient, $subject, $mail_body, $header); //mail command :)
#echo $header;
}

function send_mail($config, $text) {
echo "Saadan emaili = $text \n";
$mail = new PHPMailer;
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'telekom-smtp.estpak.ee';  // Specify main and backup SMTP servers
$mail->SMTPAuth = false;                               // Enable SMTP authentication
$mail->setFrom('xglogs@xglogs.emt.ee', 'XGLOGS Logide server');

#$Name = "XgLogs"; //senders name
#$email = "xglogs@emt.ee"; //senders e-mail adress
#$recipient = $config->config_values[errors][warning_mail];
$mail->addAddress($config->config_values[errors][warning_mail]);     // Add a recipient
#$mail_body = $text; //mail bo
$mail->Body    = $text;
#$subject = "Veateade XGLOGS masinast, check.php"; //subject
$mail->Subject =  "Veateade XGLOGS masinast, check.php"; //subject
#$header = "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields
#mail($recipient, $subject, $mail_body, $header); //mail command :)
if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}

}


function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}

?>
