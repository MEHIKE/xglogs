#!/usr/local/bin/php -q
<?php

include ('/home/webapp/phptest/bin/conf/hosts_conf.php');
#include ('/home/webapp/phplogs/bin/conf.php');
include ('/home/webapp/phptest/bin/config.class.php');


#print_r($hosts);
#print 'incoming kataloog='.@INCOMING_PATH;
#echo "\n Excludes: ".$config->config_values[exclude][vaartus3]."\n";
#print_r($config->config_values[paths]);
#var_dump($hosts);
#$con = new config();

$config = config::getInstance();

#ini_set("memory_limit","".$config->config_values[memory][mem_size]);
ini_set("memory_limit",trim($config->config_values[memory][mem_size]));

#print "Päevade arv confis= ".$config->config_values[others][days];
#print $config->config_values[paths][INCOMMING_PATH];
echo "\n\n***********************************************************************************************************************************************";
echo "Kopeerimise scripti algus: ".date("d-M-Y G:i:s",strtotime("now"))."\n\n";
print "Inncoming path= ".$config->config_values[paths][incoming_path]."\n";
$algus=strtotime("now");

#print $config->config_values[INCOMMING_PATH];
#echo ''."\n";


#print "procpath= ".$config->config_values[paths][processed_path];
#echo ''."\n";
print "Excludes: ".$config->config_values[exclude][vaartus]."\n";
print "Included: ".$config->config_values[included][vaartus]."\n";
$str = "now - ".$config->config_values[others][days]." days";
#echo "\n $str \n";
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
$incoming_files = get_files_from_path($config->config_values[paths][incoming_path]);
$processed_files = get_files_from_path($config->config_values[paths][processed_path]);
#var_dump($processed_files);'
$files = array_merge($incoming_files, $processed_files);
#print_r($files);

echo "Läheb kopimiseks, hostid: \n";
foreach($hosts as $host => $host_values )
{
#$population = number_format($population);
	echo "$host: \n";
	#foreach($host_values as $key => $value )
	{
	#	echo "$key -> $value \n";
		copy_logs($host, $host_values, $config, $paev, $files);
	}
	#echo "\n";

}
$lopp1 = strtotime("now");
$p_aeg1 = $lopp1 - $algus;
echo "\nKopeerimiseeks kulus: ".tund_from_sek($p_aeg1)."tund ".min_from_sek($p_aeg1-tund_from_sek($p_aeg1)*3600)."min ".sek_from_sek($p_aeg1)."sek\n";
echo "\nKopeerimise scripti lõpp: ".date("d-M-Y G:i:s",strtotime("now"))."\n\n";
$algus1 = $lopp1;

#logide tõstmine processed kataloogi, samal ajal igale reale kellaaega lisades, kui pole
$inc_files = get_files_from_path($config->config_values[paths][incoming_path]);
#var_dump($inc_files);
echo "Läheb failide tõstmiseks processed kataloogi: \n\n";
foreach($inc_files as $jrk_nr => $rakendus )
{
#var_dump($rakendus);
        echo "Rakendus: $rakendus , $jrk_nr , Kell: ".date("d-F-Y H:i:s",strtotime("now"))."---> \n";
			if ( $jrk_nr != 0) 
	                        kopeeri($rakendus, $config);
}
echo "\nProcessed kataloogi tõstmise scripti lõpp: ".date("d-F-Y H:i:s",strtotime("now"))."\n\n";


#echo "Aega kulus: ".((int)(($lopp-$algus)/60))."min ja ".(($lopp-$algus))."sek \n";
$lopp = strtotime("now");
$p_aeg = $lopp - $algus;
$p_aeg1 = $lopp - $algus1;
echo "Aega kulus incoming->processed tõstmiseks: ".tund_from_sek($p_aeg1)."tund ".min_from_sek($p_aeg1-tund_from_sek($p_aeg1)*3600)."min ".sek_from_sek($p_aeg1)."sek\n";
echo "Aega kulus kokku: ".tund_from_sek($p_aeg)."tund ".min_from_sek($p_aeg-tund_from_sek($p_aeg)*3600)."min ".sek_from_sek($p_aeg)."sek\n";
echo "Kettal vaba ruumi: ".((int)(disk_free_space("/")/1024/1024/1024))."Gb\n";
echo "***********************************************************************************************************************************************\n\n";


function kopeeri($filename, $config) {
				#kopeerimise funktsioon siia, edasi annme parameetrina failinime ja config muutuja
				#kopi($full_filename, $config);
        $algfail = fopen($config->config_values[paths][incoming_path].$filename, 'r');
        $loppfail= fopen($config->config_values[paths][processed_path].$filename, 'a+');
	$node = substr($filename, 0, strpos($filename, "-"));
	$kell = substr($filename, strrpos($filename, "-")+1);
	echo "Faili (node $node, kell $kell) töötlemine ja asetamine processed kataloogi: ".$config->config_values[paths][incoming_path].$filename."->".$config->config_values[paths][processed_path].$filename." \n";
	$aeg = $kell.":00:00";
        while ( !feof($algfail) ) {
                        $i++;
                        $line = fgets($algfail);
                        $row = explode("|",$line);

			#echo "asukoht=".strpos($row[0],":")."\n";
                        if ( $row[0] && $row[1] && strpos($row[0],":") == 2 ) {
                                if ( substr_count($row[0], ":") == 5) {
                                        $line=$aeg."|".$node."|DEBUG||".$line;
                                } else
                                        $aeg=$row[0];
                                if ( strpos($line, $node)<=0 )
                                        $line = $aeg."|".$node."+".$row[1]."|".$row[2]."|".$row[3]."|".$row[4]."|\n";
#."|".$node."||";
#                               if ( !$row[1]) {
#                                       $line=$row[0]."|".$node."|".$row[2]."|".$row[3]."|".$row[4]."|";
                        #.      }
                        } else {
                                #kui pole aega siis paneme esimeseks
                                if ( $row[1] ) {
                                        #$line = $aeg."|".$line;
                                        if ( strpos($line, $node)<=0 ) {
                                                $line = $node."|".$line;
                                        }
                                        $line = $aeg."|".$line;
                                }
                                else {
                                        if ( $row[0] && substr_count($row[0], ":") == 5) {
                                                $line=$aeg."|".$node."|DEBUG||".$line;
                                        } else
                                                if ( $row[0] && strpos($row[0],":") == 2 )
                                                        $line = $aeg."|".$node."|".$row[2]."|".$row[3]."|".$row[4]."|\n";
                                                else
                                                        if ( strpos($line, "at ")>0 || strpos($line, "Exception")>0 || strpos($line, "ESSAGE")>0 || strpos($line, "TACKTRACE")>0 || strpos($line,"LASS")>0 || strpos($line, "aused by")>0 || (strpos($line, "...")>0 && strpos($line, " more")>0 ) )
                                                                $line = $aeg."|".$node."|ERROR||".$line;
                                                        else
                                                                $line = $aeg."|".$node."|INFO ||".$line;
                                }
                                        #$line = $aeg."|".$node."|".$row[2]."|".$row[3]."|".$row[4]."|\n";
                        }
                        if ( strlen(trim($line))<=2 ) {
                                $line=$aeg."|".$node."|INFO ||".$line;
                                echo "Lühike rida=$line \n";
                        }
		fwrite($loppfail, $line);
        }
        fclose($algfail);
        fclose($loppfail);
        unlink($config->config_values[paths][incoming_path].$filename);
				#ja kustutamine siia
}


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

#        print "OUT= ssh -l $host_values[user] $host ls $host_values[path] \n";
#        $out = `ssh -l $host_values[user] $host "ls $host_values[path]"`;
        $result = exec("ssh -l $host_values[user] $host ls $host_values[path]", $out);
	
	if (empty($result)) {
 	#echo "*********************************\n";
		print "Polnud midagi kopida => ssh -l $host_values[user] $host ls $host_values[path] \n";
		return;
	#	echo "result = $result \n";
	}
	#print_r($out);
	foreach($out as $file ) {
		#print_r(explode(" ",str_replace("  "," ",$filename)));
		#kui failinimi on confis, exludetud, sisi ei võeta arvesse
		#echo "pos= ".substr_count($filename, '.log.')." \n";
#is_archived($filename, $config);
		$filename = $file;
		#echo "fail= ".$filename." not_excluded->".not_excluded($filename, $config)."\n";
		#echo "fail= ".$filename." to_old->".to_old($filename, $paev)." strpos=".strrpos($filename, '.log.')."\n";
		#echo "included=".$config->config_values[included][vaartus]."<";
		if ((not_excluded($filename, $config) and strrpos($filename, '.log.') > 0 and to_old($filename, $paev)==FALSE ) or 
			($config->config_values[included][vaartus] and included($filename, $config)==TRUE and to_old($filename, $paev)==FALSE and 
			not_excluded($filename, $config) ) ) { 
#			!is_copied($filename, $files) and !is_archived($filename, $config) ) {
			$filename = add_prefix($host, $filename, $config);
			$filename = add_suffix($filename, $config);
			#echo "Sain sisse \n";
			if (!is_copied($filename, $files)) {
#and !is_archived($filename, $config) ) {
#)
				echo "$filename, ";
	#			$result = exec("ssh -l $host_values[user] $host ls $host_values[path]", $out);i
			#print "path=== ".$config->config_values[paths][archive_path]." \n";

        echo "Kopeeritakse käsuga= scp $host_values[user]\@$host:$host_values[path]/$file ".$config->config_values[paths][incoming_path]."$filename \n";
        $result = exec("scp -B $host_values[user]\@$host:$host_values[path]/$file ".$config->config_values[paths][incoming_path]."$filename", $oo);

#	echo "Faili suurus: ".filesize($config->config_values[paths][incoming_path]."$filename")." \n Suurus=".formatBytes(filesize($config->config_values[paths][incoming_path]."$filename"))."\n";
	error_mes($config->config_values[paths][incoming_path].$filename, $config);


#				to_old($filename, $paev);
			}
		}
	}
   	#print_r($out);
	#$ddd = exec(`ls /logs/incoming/processed/`);
        #print($ddd);
        #$files = explode(/\n/,$out);


	return;
}


function error_mes($filename, $config) {
	$file_size=filesize($filename);
	if ($file_size > $config->config_values[errors][max_filesize]) {
		echo "VIGA xglogs masinasse logide kopeerimisel!!!!!!!!!!!!!!!!!!!!!!!! \n";
		echo "Fail ".$filename." on väga suur! \n";
		echo "Maximaalne failisuurus on määratud: (".$config->config_values[errors][max_filesize].") e. (".formatBytes($config->config_values[errors][max_filesize]).")\n";
		echo "Kopeeritud faili suurus: (".$file_size.") e. (".formatBytes($file_size).")\n";
		send_mail($config, $filename, $file_size);
		return;
	} 
        if ($file_size > $config->config_values[errors][max_filesize_warning]) {
                echo "HOIATUS xglogs masinasse logide kopeerimisel! \n";
                echo "Fail ".$filename." on suurem kui tavaliselt lubatud! \n";
                echo "Keskmine maximaalne failisuurus on määratud: (".$config->config_values[errors][max_filesize_warning].") e. (".formatBytes($config->config_values[errors][max_filesize]).")\n";
                echo "Kopeeritud faili suurus: (".$file_size.") e. (".formatBytes($file_size).")\n";
                send_mail($config, $filename, $file_size);
                return;
        }

}


function nodesize( $node ) 
{ 
    if( !is_readable($node) ) return false; 
    $blah = exec( "/usr/bin/du -sk $node" ); 
    return substr( $blah, 0, strpos($blah, 9) ); 
} 


function send_mail($config, $filename, $file_size) {
	#/bin/echo "Hoiatus: esines OutOfMemory viga KREEDO LIVE nodeA logis (tasuks moelda kreedowli1 restardile)" | /bin/mail valve@sorts.emt.ee
$Name = "XgLogs"; //senders name 
$email = "xglogs@emt.ee"; //senders e-mail adress 
#$recipient = $config->config_values[errors][error_mail]; //recipient 
if ($file_size > $config->config_values[errors][max_filesize]) {
	$recipient = $config->config_values[errors][error_mail];
	$mail_body = sprintf($config->config_values[errors][max_filesize_message],$filename,formatBytes($config->config_values[errors][max_filesize]),formatBytes($file_size)); //mail body 
	$subject = "Veateade XGLOGS masinast"; //subject 
} else {
	$recipient = $config->config_values[errors][warning_mail];
        $mail_body = sprintf($config->config_values[errors][max_filesize_warningmessage],$filename,formatBytes($config->config_values[errors][max_filesize_warning]),formatBytes($file_size)); //mail body
        $subject = "Hoiatus XGLOGS masinast"; //subject
}
$header = "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields 

mail($recipient, $subject, $mail_body, $header); //mail command :) 

}


function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
   
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
   
    $bytes /= pow(1024, $pow); 
   
    return round($bytes, $precision) . ' ' . $units[$pow]; 
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


function included($filename, $config) {
        $included = explode(',',$config->config_values[included][vaartus]);
        foreach ($included as $name) {
                if (substr_count($filename, $name) > 0 ) {
                        return TRUE;
                }
        }
        return FALSE;
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


function add_suffix($filename, $config) {
        $sufix = explode(',',$config->config_values[sufix][vaartus]);
        foreach ($sufix as $name) {
                if (substr_count($filename, $name) > 0 ) {
                        return $filename."-23";
                }
        }
        return $filename;
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

function is_archived($filename, $config) {
	$pos = strpos($filename, "-");
	$posr = strrpos($filename, "-");
	$name = substr($filename, $pos+1, $posr-$pos-1);
	#echo "nimiiiiiiiiiii= $name ja $name".".gz \n";	
	if (is_file($config->config_values[paths][archive_path].$name) or is_file($config->config_values[paths][archive_path].$name.".gz")) {
	#	echo "arhiveeritud \n";
		return TRUE;
	}
	#echo "Ei ole arhiveeritud \n";
	return FALSE;
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

?>
