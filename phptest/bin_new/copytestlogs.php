#!/usr/bin/php -q
<?php

include ('/home/webapp/phptest/bin_new/conf/hosts_conf.php');
#include ('/home/webapp/phplogs/bin_veel/conf.php');
include ('/home/webapp/phptest/bin_new/config.class.php');

ini_set("memory_limit","2G");

#print_r($hosts);
#print 'incoming kataloog='.@INCOMING_PATH;
#echo "\n Excludes: ".$config->config_values[exclude][vaartus3]."\n";
#print_r($config->config_values[paths]);
#var_dump($hosts);
#$con = new config();

$config = config::getInstance();
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
print "Gzips: ".$config->config_values[gzip][vaartus]."\n";
$str = "now - ".$config->config_values[others][days]." days";
#echo "\n $str \n";
$paev = strtotime($str);
#$paev = strtotime("now - 3 days");
$mydate = date("Y-m-d-G",$paev);
print "Viimane copytav aeg= $mydate \n \n";

#$excludes = $config->config_values[exclude][vaartus];
# kataloogi olemasolu kontroll
if (is_dir($config->config_values[paths][incoming_path])) {
# echo "Kataloog on olemas: ".$config->config_values[paths][incoming_path]."\n";
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
print_r($hosts);

echo "Läheb hostide töötlemiseks: \n";
foreach($hosts as $host => $host_values )
{
#$population = number_format($population);
	echo "Host= $host: \n";
	#foreach($host_values as $key => $value )
	{
		echo "$key -> $value \n";
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
			if ( not_excluded($rakendus, $config) and $jrk_nr != 0 and !is_gzip($rakendus, $config) and !is_repl_gz($rakendus, $config) ) 
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

function is_time_reg($line) {
	return preg_match ("/^([0-9]{2}):([0-9]{2}):([0-9]{2})/", $line);
}

function is_mac_reg($line) {
	if (preg_match ("/^([0-9A-F]{2}):([0-9A-F]{2}):([0-9A-F]{2}):([0-9A-F]{2})/", trim($line))) {
		return TRUE;
	}
	return FALSE;
}

function mac_line($line) {
        if (preg_match ("/^([0-9A-F]{2}):([0-9A-F]{2}):([0-9A-F]{2}):([0-9A-F]{2})/", trim($line))) {
                $order   = array("\r\n", "\n", "\r");
                $replace = '';
                return str_replace($order, $replace, trim($line));
        }
        return $line;
}


function change_level($filename, $config) {
//	$change = 0;
	        $change = explode(',',$config->config_values[change_level][vaartus]);
        foreach ($change as $name) {
                if (substr_count($filename, $name) > 0 ) {
			echo "LEVEL vales kohas, muudan -> filename=".$filename." change_level filename=".$name." \n";
                        return 1;
                }
        }
        return 0;

//	return $change;
}

function kopeeri($filename, $config) {
				#kopeerimise funktsioon siia, edasi annme parameetrina failinime ja config muutuja
				#kopi($full_filename, $config);
        $algfail = fopen($config->config_values[paths][incoming_path].$filename, 'r');
        $loppfail= fopen($config->config_values[paths][processed_path].$filename, 'a+');
	$node = substr($filename, 0, strpos($filename, "-"));
	$change = change_level($filename, $config);
	$kell = substr($filename, strrpos($filename, "-")+1);
	$file_aeg = substr($filename, strpos($filename, ".log." )+5, 10);
	echo "Faili (node $node, kell $kell) töötlemine ja asetamine processed kataloogi: ".$config->config_values[paths][incoming_path].$filename."->".$config->config_values[paths][processed_path].$filename." fileaeg=".$file_aeg."  change=".$change." \n";

	#if (is_gzip($filename, $config)) {
	#	echo "enne gz olnud failid on ilmselt suured ja saavad kopeeritud otse copy_logs funktsioonis -> ".$config->config_values[paths][processed_path].$filename." fileaeg=".$file_aeg."." \n";
	#}
	$aeg = $kell.":00:00";

	#$file_aeg = substr($filename, strpos($filename, ".log." )+5, 10);

	$read_bytes = 16777216;
	if (!feof($algfail)) {
		$current_line = stream_get_line($algfail, $read_bytes, "\n");
		$current_line=check_line($file_aeg, $current_line);
		$current_line = repl_time($current_line);
		#echo "currentline=".$current_line." \n";
        	//$current_line = fgets($algfail);
	}
	else {
		$current_line = '';
	}
	$error_line = false;
        while ( !feof($algfail) ) {
                        $i++;

			#if ($change==1) {
			#	$current_line = repl_level($current_line);
			#}
			
                        $new_line = stream_get_line($algfail, $read_bytes, "\n");//fgets($algfail);
                        #if ($change==1) {
                        #        $new_line = repl_level($new_line);
                        #}

			$new_line=check_line($file_aeg, $new_line);
			$new_line = repl_time($new_line);
                        //$row = explode("|",$current_line);

			//echo "newline=".$new_line." \n";

			//kui hetke real on päev siis teeb tavalise töötluse
			if (is_time_reg($current_line) and !$error_line and !is_mac_reg($current_line)) {
				$row = explode("|",$current_line);
				$aeg=$row[0];
				$aeg=check_aeg($file_aeg, $aeg);
				$current_line = make_line($current_line, $row, $node, $aeg, $filename);
				$current_line = check_last_param($current_line);
				//fwrite($loppfail, $current_line);
			} 
			//kui järgmisel real pole kuupäeva siis lisab praeguse rea otsa
			if (!is_time_reg($new_line) and !is_mac_reg($new_line)) {
				$current_line = $current_line."\n".$new_line;
				$error_line = true;
			}
                        if (is_mac_reg($new_line)) {
                                $current_line = $current_line."\n".mac_line($new_line);
                                $error_line = true;
                        }
			
			//$new_line=check_line($file_aeg, $new_line);			
			//$current_line=check_line($file_aeg, $current_line);

			//kui uuel real on kuupäev siis kirjutab otse faili
			if (is_time_reg($new_line) and !is_mac_reg($new_line)) {

                	        if ($change==1) {
        	                        $current_line = repl_level($current_line);
	                        }

                        	fwrite($loppfail, $current_line."\r\n");

                                //if (strcmp( $node , "wally") == 0 ) {
                                //        echo "kirjutatan faili rea, kuna praegune sisaldab aega.   current_line=".$current_line."  failinimi=".$loppfail." \n";
                                //}

				$current_line = $new_line;
				$error_line = false;
			} else {
				//if (strcmp( $node , "wally") == 0 ) {
				//	echo "ei kirjutata faili eelmist rida, kuna praegune ei sisalda aega.  node=".$node."  new_line=".$new_line."  current_line=".$current_line." \n";
				//}
			}
			//$current_line = $new_line;
			$new_line='';
        }
        fclose($algfail);

        if (is_time_reg($current_line) and !$error_line and !is_mac_reg($current_line)) {
		 if ($change==1) {
                        $current_line = repl_level($current_line);
                }

	        $row = explode("|",$current_line);
                $aeg=$row[0];
                $current_line = make_line($current_line, $row, $node, $aeg, $filename);
		$current_line=check_line($file_aeg, $current_line);
                //fwrite($loppfail, $current_line);
        }
	fwrite($loppfail, $current_line."\r\n");
        fclose($loppfail);
        unlink($config->config_values[paths][incoming_path].$filename);
				#ja kustutamine siia
}


function repl_level($line1) {
        $row1 = explode("|",$line1);
        $line2='';
        $count = count($row1);
        if ( is_time_reg($line1) and $count > 3 ) {
                
                        $ajut = $row1[2];
			$row1[2] = $row1[3];
			$row1[3] = $ajut;
                        $arr = implode("|", $row1);
                        
                        if (endsWith($arr, "|")) {
                                return $arr;
                        } else {
                                return $arr."|";
                        }
        }
	return $line1;
}



function check_last_param($line1) {
        $row1 = explode("|",$line1);
        $line2='';
	$count = count($row1);
        if ( is_time_reg($line1) and $count > 3 ) {
		if (strcmp( $row1[count-1] , $row1[count-2]) == 0 ) {
			$row1[count-1]="";
			$arr = implode("|", $row1);
			if (strcmp( $node , "wally") == 0 ) {
				echo "viimane parameeter = eelviimane kirjutame kokku, lause=".$arr." \n";
			}
	                if (endsWith($arr, "|")) {
        	                return $arr;
                	} else {
	                        return $arr."|";
        	        }
		}
                //$aeg = str_replace(",",".",$row1[0]);
                //$row1[0]=$aeg;
                //$arr = implode("|", $row1);
                //if (endsWith($arr, "|")) {
                //        return $arr;
                //} else {
                //        return $arr."|";
                //}
        }
        return $line1;

}

function make_line($line, $row, $node, $aeg, $filename) {
                        if ( $row[0] && $row[1] && strpos($row[0],":") == 2 ) {
                                if ( substr_count($row[0], ":") == 5) {
                                        $line=$aeg."|".$node."|DEBUG||".$line;
                                } else
                                        $aeg=$row[0];
				$file_aeg = substr($filename, strpos($filename, ".log." )+5, 10);
				$aeg = check_aeg($file_aeg, $aeg);
                                if ( strpos($line, $node)<=0 ) {
                                        //$line = $aeg."|".$node."+".$row[1]."|".$row[2]."|".$row[3]."|".$row[4]."|\n";
					$line = $aeg."|".$node."+".$row[1]."|".$row[2]."|".$row[3]."|".$row[4]."|";
				}
				if ( $row[5] and substr_count($line, '|')<6 ) {
                                if (strcmp( $node , "wally") == 0 ) {
                                        #echo "4. Vali 5 olemas.   line ennem=".$line."  lisame row[5]=".$row[5]."| \n";
                                }

					$line = $line.$row[5]."|";
				}
                                if ( $row[6] and !compare( $row[5], $row[6]) and  substr_count($line, '|')<7  ) {

                                if (strcmp( $node , "wally") == 0 ) {
                                        #echo "1. Vali 6 olemas.   line ennem=".$line."  lisame row[6]=".$row[6]."| \n";
                                }


                                        $line = $line.$row[6]."|";
                                }

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
                                                if ( $row[0] && strpos($row[0],":") == 2 ) {
                                                        //$line = $aeg."|".$node."|".$row[2]."|".$row[3]."|".$row[4]."|\n";
							$line = $aeg."|".$node."|".$row[2]."|".$row[3]."|".$row[4]."|";
			                                if ( $row[5]  and substr_count($line, '|')<6 ) {
                                if (strcmp( $node , "wally") == 0 ) {
                                        #echo "3. Vali 5 olemas.   line ennem=".$line."  lisame row[5]=".$row[5]."| \n";
                                }

                        			                $line = $line.$row[5]."|";
			                                }
                        			        if ( $row[6] and !compare( $row[5], $row[6])  and substr_count($line, '|')<7  ) {

                                if (strcmp( $node , "wally") == 0 ) {
                                        #echo "2. Vali 6 olemas.   line ennem=".$line."  lisame row[6]=".$row[6]."| \n";
                                }


                                			        $line = $line.$row[6]."|";
				                        }

						}
                                                else
                                                        if ( strpos($line, "at ")>0 || strpos($line, "Exception")>0 || strpos($line, "ESSAGE")>0 || strpos($line, "TACKTRACE")>0 || strpos($line,"LASS")>0 || strpos($line, "aused by")>0 || (strpos($line, "...")>0 && strpos($line, " more")>0 ) )
                                                                $line = $aeg."|".$node."|ERROR||".$line;
                                                        else
                                                                $line = $aeg."|".$node."|INFO ||".$line;
                                }
                                        #$line = $aeg."|".$node."|".$row[2]."|".$row[3]."|".$row[4]."|\n";
                        }
                        if ( !isset($line) || !$line || strlen(trim($line))<=7 ) {
                                $line=$aeg."|".$node."|INFO ||".$line;
                                #echo "Lühike rida=$line \n";
                        }

	return $line;
}


function compare( $str1, $str2 )  {
	return strcmp($str1, $str2) == 0; 
}

/**
 * StartsWith
 * Tests if a text starts with an given string.
 *
 * @param     string
 * @param     string
 * @return    bool
 */
function StartsWith($Haystack, $Needle){
    // Recommended version, using strpos
    return strpos($Haystack, $Needle) === 0;
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function check_aeg($file_aeg, $aeg) {
	if ( StartsWith($aeg, $file_aeg) ) {
               $uus= trim(substr($aeg, 11, strlen($aeg)-11+1));
		$uus = str_replace(",",".",$uus);
		#echo "muudame aega, vana=".$aeg."  uus=".$uus."   failiaeg=".$file_aeg." \n";
		return $uus;
        }
	return $aeg;
}


function repl_time($line1) {
	$row1 = explode("|",$line1);
        $line2='';
	if ( is_time_reg($line1) ) {
		$aeg = str_replace(",",".",$row1[0]);
                $row1[0]=$aeg;
		$arr = implode("|", $row1);
		if (endsWith($arr, "|")) {
			return $arr;
		} else {
			return $arr."|";
		}
	}
	return $line1;

	if ( $row1[0] ) {
		$aeg = str_replace(",",".",$row1[0]);
		$line2=$aeg."|";
	}
        if ( $row1[1] ) {
                $line2=$line2.$row1[1]."|";
        }
        if ( $row1[2] ) {
                $line2=$line2.$row1[2]."|";
        }
        if ( $row1[3] ) {
                $line2=$line2.$row1[3]."|";
        }
        if ( $row1[4] ) {
                $line2=$line2.$row1[4]."|";
        }
        if ( $row1[5] ) {
                $line2=$line2.$row1[5]."|";
        }
        if ( $row1[6] ) {
                $line2=$line2.$row1[6]."|";
        }

	return $line2;
}


function check_line($file_aeg, $line) {
        if ( StartsWith($line, $file_aeg) ) {
                $uus= trim(substr($line, 11, strlen($line)-11+1));

                #$uus = str_replace(",",".",$uus);
                #echo "check_line->muudame aega, vana=".$line."  uus=".$uus."   failiaeg=".$file_aeg." \n";
                return $uus;
        }
        return $line;
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
 echo "copy_logs => algus \n";
        print "OUT= ssh -l $host_values[user] $host ls -R $host_values[path] \n";
#        $out = `ssh -l $host_values[user] $host "ls $host_values[path]"`;
        $result = exec("ssh -l $host_values[user] $host ls -R $host_values[path]", $out);
	
	if (empty($result)) {
 	#echo "*********************************\n";
		print "Polnud midagi kopida => ssh -l $host_values[user] $host ls -R $host_values[path] \n";

if (empty($out)) {
} else
{
	if (strrpos($out, 'Host key verification failed') > 0) {
		send_error($config, $host);
	}
}
		return;
	#	echo "result = $result \n";
	}
	print_r($out);
	echo "failid: $out \n\n";
	foreach($out as $file ) {
		#print_r(explode(" ",str_replace("  "," ",$filename)));
		#kui failinimi on confis, exludetud, sisi ei võeta arvesse
		#echo "pos= ".substr_count($filename, '.log.')." \n";
#is_archived($filename, $config);
		$filename = $file;
		$messaging_fname=$filename;
		echo "fail= ".$filename." not_excluded->".not_excluded($filename, $config)."\n";
		echo "fail= ".$filename." strpos .log. ->strpos=".strrpos($filename, '.log.')."\n";
		echo "fail= ".$filename." to_old -> to_old=".to_old($filename, $paev)."\n";
		#echo "fail= ".$filename." to_old->".to_old($filename, $paev)." strpos=".strrpos($filename, '.log.')."\n";
		if (not_excluded($filename, $config) and strrpos($filename, '.log.') > 0 and to_old($filename, $paev)==FALSE ) { 
			#!is_copied($filename, $files) and !is_archived($filename, $config) ) {
			$hostname = check_host($host, $config);
        echo "failinime kontroll pärast = ".check_filename($filename, $host, $config)." / ennem=".$filename."  host=".$host."\n";
        $filename = check_filename($filename, $host, $config);

			$filename = add_pre($host, $filename, $config);
			echo "Filename1= $filename (host=$host)  \n ";
				#$filename = add_prefix($host, $filename, $config);
			$filename = add_prefix($hostname, $filename, $config);
			echo "Filename2= $filename (host=$hostname)  \n ";
			if (!is_gzip($filename, $config)) {
				$filename = add_suffix($filename, $config);
				echo "Filename3= $filename \n ";
			}
			echo "is_copied $filename = ".!is_copied($filename, $files)." \n ";
			//is_archived($filename, $config);
			#echo "Sain sisse \n";
			if (!is_copied($filename, $files)) {
				#and !is_archived($filename, $config) ) {
				#)
				echo "$filename, ";
				#$result = exec("ssh -l $host_values[user] $host ls $host_values[path]", $out);
				#print "path=== ".$config->config_values[paths][archive_path]." \n";

	if (!messaging($messaging_fname, $config)) {
         echo "Kopeeritakse käsuga= scp $host_values[user]\@$host:$host_values[path]/$file ".$config->config_values[paths][incoming_path]."$filename \n";
         $result = exec("scp -B $host_values[user]\@$host:$host_values[path]/$file ".$config->config_values[paths][incoming_path]."$filename", $oo);
	}
	if (messaging($messaging_fname, $config)) {
	 echo "Kopeeritakse käsuga(messaging)= scp $host_values[user]\@$host:$host_values[mess_path]/$file ".$config->config_values[paths][incoming_path]."$filename \n";
	 $result = exec("scp -B $host_values[user]\@$host:$host_values[mess_path]/$file ".$config->config_values[paths][incoming_path]."$filename", $oo);
	}
	$filen=$filename;
	if (is_gzip($filename, $config)) {
		echo "Pakitakse lahti käsuga= gunzip -f ".$config->config_values[paths][incoming_path]."$filename \n";
		$result1 = exec("gunzip -f ".$config->config_values[paths][incoming_path].$filename, $out1);
		$filename = substr($filename, 0, strpos($filename, '.gz'));
		echo "failinimi pärast lahti pakkimist: ".$filename." \n";
		$fname = $config->config_values[paths][incoming_path].$filename;
		$filename = add_suffix($filename, $config);
		echo "enne gz olnud failid on ilmselt suured ja saavad imuuvitud otse siin copy_logs funktsioonis -> ".$config->config_values[paths][processed_path].$filename." \n";
		$result = exec("mv $fname ".$config->config_values[paths][processed_path]."$filename", $oo);
                echo "addsuffix Filename4= $filename \n ";

	}
        if (is_repl_gz($filen, $config)) {
                #echo "Pakitakse lahti käsuga= gunzip -f ".$config->config_values[paths][incoming_path]."$filename \n";
                #$result1 = exec("gunzip -f ".$config->config_values[paths][incoming_path].$filename, $out1);
                #$filename = substr($filename, 0, strpos($filename, '.gz'));
                echo "failinimi enen pakkimist: ".$filen." \n";
                $fname = $config->config_values[paths][incoming_path].$filen;
                $filename = add_suffix($filen, $config);
                echo "siin märgitud failid on ilmselt suured ja saavad muuvitud otse siin copy_logs funktsioonis -> ".$config->config_values[paths][processed_path].$filename." \n";
                $result = exec("mv $fname ".$config->config_values[paths][processed_path]."$filename", $oo);
                echo "addsuffix Filename4= $filename \n ";

        }

	$f_name = $config->config_values[paths][incoming_path].$filename;
	echo "taisfailinime on Filename4a= $f_name \n ";
	 if (!is_gzip($filename, $config)) {

		error_mes($f_name, $config);
	}

#				to_old($filename, $paev);
			} else echo "is_copied... \n";
		} else echo "excluded... \n";
	}
   	#print_r($out);
	#$ddd = exec(`ls /logs/incoming/processed/`);
        #print($ddd);
        #$files = explode(/\n/,$out);


	return;
}

function messaging($filename, $config) {
        $messaging = explode(',',$config->config_values[messaging][vaartus]);
	$pos = strpos($filename, ".log" );
	$sfname = substr($filename, 0, $pos);
        #echo "\nMessage check sfname=\"$sfname\" filename=\"$filename\"\n";

        foreach ($messaging as $name) {
                #echo "M: $name\n";
                if ($name==$sfname) {
                    # if (substr_count($name, $sfname) > 0 ) {
                    return TRUE;
                }
        }
        return FALSE;
}


function not_excluded($filename, $config) {
	$excluded = explode(',',$config->config_values[exclude][vaartus]);
	#$gz = explode(',',$config->config_values[gzip][vaartus]);
	foreach ($excluded as $name) {
		if (substr_count($filename, $name) > 0 ) {
			echo "not_excluded   filename=$filename  vaartus=$name  \n";
			if (!is_gzip($filename, $config)) {
				echo "not_excluded   filename=$filename  vaartus=$name is_gzip RETURN FALSE \n";
				return FALSE;
			}
		}
	}
	echo "not_excluded   filename=$filename  vaartus=$name is_gzip RETURN TRUE \n";
	return TRUE;
}

function is_gzip($filename, $config) {
        #$excluded = explode(',',$config->config_values[exclude][vaartus]);
        $gzipped = explode(',',$config->config_values[gzip][vaartus]);
        foreach ($gzipped as $name) {
                if (substr_count($filename, $name) > 0 ) {
			echo "is_gzip= filename: $filename  ja  name: $name  \n";
                        return TRUE;
                }
        }
        return FALSE;
}

function is_repl_gz($filename, $config) {
        #$excluded = explode(',',$config->config_values[exclude][vaartus]);
        $gzipped = explode(',',$config->config_values[repl_gz][vaartus]);
        foreach ($gzipped as $name) {
                if (substr_count($filename, $name) > 0 ) {
                        echo "is_repl_gzip= filename: $filename  ja  name: $name  \n";
                        return TRUE;
                }
        }
        return FALSE;
}

function add_pre($host, $filename, $config) {
        $prefix = explode(',',$config->config_values[addpre][vaartus]);
	#$filename = str_replace("-","_",$filename);
	#echo "failinime kontroll pärast = ".check_filename($filename, $host, $config)." / ennem=".$filename."\n";
	#$filename = check_filename($filename, $host, $config);
        foreach ($prefix as $name) {
		if ($name != null) {
                if (substr_count($host, $name) > 0) {
			$uusname = str_replace("-","",$name);
			#echo "TELIA uus filename=".$uusname."_".$filename."\n";
                        return $uusname."_".$filename;
			#echo "TELIASONERA TAANI failid    ->  ts_".$filename."\n";
			#echo check_host($host, $config);
                }
        }}
        return $filename;
}

function add_prefix($host, $filename, $config) {
        $prefix = explode(',',$config->config_values[prefix][vaartus]);
        foreach ($prefix as $name) {
                if (substr_count($filename, $name) > 0 and substr_count($filename, $host) <= 0 ) {
                        return $host."-".$filename;
			#return check_host($host, $config)."-".$filename;
                }
        }
	#siin peaks kui pole filename osana prefixit selle ikkagi lisama automaatselt
	if (substr_count($filename, $host) <= 0 ) {
		return $host."-".$filename;
	}
        return $filename;
}


function repl_min($line) {
        if (preg_match ("/^([0-9A-F]{2}):([0-9A-F]{2}):([0-9A-F]{2}):([0-9A-F]{2})/", trim($line))) {
                $order   = array("\r\n", "\n", "\r");
                $replace = '';
                return str_replace($order, $replace, trim($line));
        }
        return $line;
}

function check_host($host, $config) {
        $prefix = explode(',',$config->config_values[addpre][vaartus]);
        foreach ($prefix as $name) {
		if ($name != null) {
                if (substr_count($host, $name) > 0) {
                        #return "ts_".$filename;
			#$hostname = explode('-',$host);
                        #echo "TELIASONERA HOST-i ymbernimetamine   ->  ".$hostname[0]."ts\n";
			$hostname = str_replace("-","_",$host);
			#echo "TELIASONERA HOST-i ymbernimetamine   ->  ".$hostname."\n";
			return $hostname;
                }
        }}
        return $host;
}

function add_suffix($filename, $config) {
        $sufix = explode(',',$config->config_values[sufix][vaartus]);
        foreach ($sufix as $name) {
		if (!(empty($filename) || empty($name))) {
	                if (substr_count($filename, $name) > 0 ) {
        	                return $filename."-23";
                	}
		}
        }
	#siin peaks otsima üles .log. asukoha ja leidma mitmest osast koosneb - vahel, kui on 2014-06-31 3osa, siis lisab automaatselt -23
	$len_fname = strlen($filename);
 	$pos = strpos($filename, ".log." );
	$aeg = substr($filename, $pos+5, $len_fname-$pos+5);
	#$explod = explode('-',$filename);
	$arv = substr_count($aeg, "-");
	echo "aeg= $aeg ja add_suffix lõpus kriipsude arv: $arv \n";	
 	if (substr_count($aeg, "-") == 2) {
		echo "failinime lõpus puudub tundide koht, lisame selle... filename=$filename \n";
		return $filename."-23";
	}	
        return $filename;
}

function check_filename($filename, $host, $config) {
	#$prefix = explode(',',$config->config_values[addpre][vaartus]);
	$pos = strpos($filename, ".log." );
	$len_fname = strlen($filename);
	$aeg = substr($filename, $pos+5, $len_fname-$pos+5);
	#echo "filename=".$filename." host=".$host." aeg=".$aeg." pos+5=".($pos+5)." len_fname-pos+5=".($len_fname-$pos+5)." aeg=".$aeg."\n";
	$fname = substr($filename, 0, $pos);
	echo "fname=".$fname." filename=".$filename." host=".$host." aeg=".$aeg." pos+5=".($pos+5)." len_fname-pos+5=".($len_fname-$pos+5)." aeg=".$aeg."\n";
	#foreach ($prefix as $name) {
        #        if ($name != null) {
                	if (substr_count($fname, $host) > 0) {
				$len = strlen($fname);
				$len_host = strlen($host)+1;
				$fname = substr($fname, $len_host, $len-$len_host );
				$fname = str_replace("-","_",$fname);
				echo "hostinimi 0n failinime sees, peale töötlust=".$fname."\n";
				return $host."-".$fname.".log.".$aeg;
			}
	#	}
	#}
	$fname = str_replace("-","_",$fname);
	echo "hostinime pole=".$fname."\n";
	return $fname.".log.".$aeg;
}

function to_old($filename, $paev) {
	$aeg = substr($filename, strpos($filename, ".log." )+5, 10);
#        echo "aeg= $aeg \n";
#2009-02-02
#0123456789
	if ($aeg == 'gz')
		return FALSE;

	echo "filetime= $aeg filename= $filename \n";

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
	echo "nimiiiiiiiiiii= $name ja $name".".gz ja filename= $filename\n";	
	if (is_file($config->config_values[paths][archive_path].$name) or is_file($config->config_values[paths][archive_path].$name.".gz")) {
	#	echo "arhiveeritud \n";
		return TRUE;
	}
	#echo "Ei ole arhiveeritud \n";
	return FALSE;
}


function is_copied($filename, $files) {
	$fils = $filename;
	if (is_gzip($filename, $config)) {
		$fils = substr($filename, 0, strpos($filename, '.gz'));
                echo "is_copied    gz failinimi pärast .gz eraldamist: ".$fils." \n";
	}

	foreach ($files as $name) {
		if (substr_count($fils, $name) > 0 or (substr_count($fils, $name) > 0 and substr($name, "_jdbc") > 0 and substr($fils, "_jdbc") > 0)) {
			echo "filename=$fils ja name=$name   is_copied=TRUE   \n";
 			echo "esimene võrldus: ".(substr_count($fils, "mapp_mml") > 0 and substr_count($name, "mapp_mml") < 0 )." \n";
			echo "teine võrdlus:   ".(substr_count($fils, "mapp_mml") < 0)." \n";
			echo "kolmas võrdlus:   ".(substr_count($fils, "mapp_mml") > 0)." \n";
			echo "neljas võrdlus:   ".(substr_count($name, "mapp_mml") < 0 )." \n";
			if (substr_count($fils, "mapp_mml") > 0 and substr_count($name, "mapp_mml") > 0 )
				return TRUE;
			if (substr_count($fils, "mapp_mml") > 0)
				echo "mapp_mml!=mml \n";
			else
				return TRUE;
		}
	
		#echo "test is_copied= $name ja $fils  = FALSE \n";
	}
	return FALSE;
}

function error_mes($filename, $config) {
        $file_size=filesize($filename);
        if ($file_size > $config->config_values[errors][max_filesize]) {
                echo "VIGA xglogs masinasse logide kopeerimisel!!!!!!!!!!!!!!!!!!!!!!!! \n";
                echo "Fail ".$filename." on väga suur! \n";
                echo "Maximaalne failisuurus on määratud: (".$config->config_values[errors][max_filesize].") e. (".formatBytes($config->config_values[errors][max_filesize]).")\n";
                echo "Kopeeritud faili suurus: (".$file_size.") e. (".formatBytes($file_size).")\n";
                #send_mail($config, $filename, $file_size);
                return;
        }
        if ($file_size > $config->config_values[errors][max_filesize_warning]) {
                echo "HOIATUS xglogs masinasse logide kopeerimisel! \n";
                echo "Fail ".$filename." on suurem kui tavaliselt lubatud! \n";
                echo "Keskmine maximaalne failisuurus on määratud: (".$config->config_values[errors][max_filesize_warning].") e. (".formatBytes($config->config_values[errors][max_filesize]).")\n";
                echo "Kopeeritud faili suurus: (".$file_size.") e. (".formatBytes($file_size).")\n";
                #send_mail($config, $filename, $file_size);
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

function send_error($config, $server) {
        #/bin/echo "Hoiatus: esines OutOfMemory viga KREEDO LIVE nodeA logis (tasuks moelda kreedowli1 restardile)" | /bin/mail valve@sorts.emt.ee
$Name = "XgLogs ERROR"; //senders name
$email = "xglogs@emt.ee"; //senders e-mail adress
#$recipient = $config->config_values[errors][error_mail]; //recipient
if ($file_size > $config->config_values[errors][max_filesize]) {
        $recipient = $config->config_values[errors][error_mail];
        $mail_body = sprintf($config->config_values[errors][error_server_message],$server); //mail body
        $subject = "Veateade XGLOGS masinast"; //subject
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

?>
