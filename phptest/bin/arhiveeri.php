#!/usr/local/bin/php -q
<?php

#ini_set("memory_limit","2G");

include ('/home/webapp/phptest/bin/config.class.php');

$config = config::getInstance();

#ini_set("memory_limit","".$config->config_values[memory][mem_size]);
ini_set("memory_limit",trim($config->config_values[memory][mem_size]));

echo "\n\nArhiveerimise scripti algus: ".date("d-F-Y H:i:s",strtotime("now"))."\n\n";
echo "Kettal vaba ruumi: ".((int)(disk_free_space("/")/1024/1024/1024))."Gb\n";
echo "Failide path= ".$config->config_values[paths][processed_path]."\n";
echo "Arhiivi path= ".$config->config_values[paths][archive_path]."\n";
$algus=strtotime("now");

$str = "now - ".$config->config_values[others][days]." days";
$paev1 = strtotime($str);
$mydate = date("Y-m-d",$paev1);
echo "See päev ja vanemad failid kustutatatakse peale arhiveerimist= $mydate \n \n";

$tana = date("Y-m-d",strtotime("now"));

#echo "Aega kulus: ".tund_from_sek(5362)."tund ".min_from_sek(5362-tund_from_sek(5362)*3600)."min ".sek_from_sek(5362)."sek\n";
#return;

# kataloogi olemasolu kontroll
if (is_dir($config->config_values[paths][processed_path])) {
 #echo "Kataloog on olemas: ".$config->config_values[paths][incoming_path]."\n";
} else {
 echo "Confis antud kataloog pole tegelikult kataloog: ".$config->config_values[paths][processed_path]."\n";
}

# failide massiiv, kus kirjas koik processed kataloogi failid
$files = get_files_from_path($config->config_values[paths][processed_path]);

echo "Läheb arhiveerimiseks: \n";
foreach($files as $rakendus => $paevad )
{
	echo "\n\nRakendus: $rakendus , Kell: ".date("d-F-Y H:i:s",strtotime("now"))."---> \n";
	foreach($paevad as $paev => $tunnid )
	{
		#echo "Päevad: $paev -> $tunnid \n";
		if ($paev != $tana) {
			echo "Rakendus: $rakendus , Päevad: $paev -> $tunnid Kell: ".date("d-F-Y H:i:s",strtotime("now"))."\n";
		        #$hour_files = $files['dealgate']['2009-07-15'];
			$hour_files = $files[$rakendus][$paev];
	        	ksort($hour_files);
			arhiveeri($hour_files, $rakendus, $paev, $config);
			delete_old_files($hour_files, $rakendus, $paev, $paev1, $config);
		} else {
			#echo "Tänast päeva ".$paev." siin ei arhiveeri, seda tehakse iga tund \n";
		}
	}
}
echo "\nArhiveerimise scripti lõpp: ".date("d-F-Y H:i:s",strtotime("now"))."\n\n";
$lopp = strtotime("now");
#echo "Aega kulus: ".((int)(($lopp-$algus)/60))."min ja ".(($lopp-$algus))."sek \n";
#echo "Aega kulus: ".tund_from_sek($lopp-$algus).min_from_sek($lopp-$algus).sekundid($lopp-$algus)."\n";
$p_aeg = $lopp - $algus;
echo "Aega kulus: ".tund_from_sek($p_aeg)."tund ".min_from_sek($p_aeg-tund_from_sek($p_aeg)*3600)."min ".sek_from_sek($p_aeg)."sek\n";
#echo "Aeg: ".date("H:i:s",strtotime($p_aeg))."\n";
echo "Kettal vaba ruumi: ".((int)(disk_free_space("/")/1024/1024/1024))."Gb\n\n";


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
	#	return " 0".$time."min";
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

function delete_old_files($files, $rakendus, $paev, $vanapaev, $config) {
	if (strtotime($paev) <= $vanapaev ) {
		echo "\n\nVanemate failide kustutamine--->\n";
        	foreach($files as $tund => $file ) {
                	#echo "tund= $tund , ";
	                #avatakse kõik failid ja loetakse esimene rida mällu
        	        foreach($file as $host => $filename) {
				#echo "failinimi= ".$filename." Failipaev= ".$vanapaev." paev= ".strtotime($paev)."\n";
				if (strtotime($paev) <= $vanapaev ) {
                        		echo "Kustutan faili: ".$config->config_values[paths][processed_path].$filename."\n";
					if (file_exists($config->config_values[paths][processed_path].$filename)) {
						unlink($config->config_values[paths][processed_path].$filename);
					}
					else {
						echo "Ei leidnud sellist faili: ".$config->config_values[paths][processed_path].$filename."\n";
					}
				}
			}
		}
	}
}

function make_dir($paev) {
        $dir = explode('-', $paev);
        return $dir[0].'/'.$dir[1].'/'.$dir[2].'/';
}

function arhiveeri($files, $rakendus, $paev, $config) {
        #avatakse tulemusfail
#        $outfile = @fopen($config->config_values[paths][archive_path].$rakendus.".log.".$paev, "w");
        $write_bytes = 33554432; #32Mb
        #$write_bytes = 67108864; #64M

 #       stream_set_write_buffer($outfile , $write_bytes );

 #       $read_bytes =  16777216; #16Mb
        mkdir ($config->config_values[paths][archive_path].make_dir($paev), 0755, true);
        $read_bytes = 1048576;

        $null_time = null_time();
        #$read_bytes =  67108864; #64M
        #$read_bytes = 16777216; #16Mb

        #kui ainult yhe nodega tegu, siis ei pea sorteerima, piisab failide kokkuliitmisest
        if (is_one_node($files)) {
                echo "Kasutuses üks node, tunnifailid võib lihtsalt kokku liita \n";
                merge_files($files, $config, $rakendus, $paev);
        } else {
                echo "Kasutuses palju nodesid, arvutame kokku, mergeme \n";
                
		#käiakse üle kõik tunnifailid järjest
		foreach($files as $tund => $file ) {
                        echo "tunni algusaeg=".date("d-F-Y H:i:s",strtotime("now"))."\n";
                        echo "tund= $tund , \n";

        $outfile = @fopen($config->config_values[paths][archive_path].make_dir($paev).$rakendus.".log.".$paev, "w");
        $write_bytes = 33554432; #32Mb
        stream_set_write_buffer($outfile , $write_bytes );

                        #loetakse järjest tunnifaile - hostid võetakse sisemises korduses
                        #avatakse kõik failid ja loetakse need mälumassiividesse
                        $summa=0;
                        foreach($file as $host => $filename) {
                                $failid[$filename][handle] = @fopen($config->config_values[paths][processed_path].$filename, "r");
                                if ($failid[$filename][handle]) {
                                        $ii = 0;
                                        while (!feof($failid[$filename][handle])) {
                                              $failid[$filename][line][$ii] = stream_get_line($failid[$filename][handle], $read_bytes, "\n");
                                              $failid[$filename][time][$ii] = find_time_from_line($failid[$filename][line][$ii], $paev);
                                              $ii++;
                                        }
                                        fclose($failid[$filename][handle]);
                                        $failid[$filename][line][$ii] = "END_OF_FILE";
                                        $failid[$filename][time][$ii] = $null_time;
                                        $failid[$filename][jrk] = 0;
                                        $failid[$filename][max] = $ii;
                                        $summa = $summa + $ii;
                                        echo "file=$filename ja ridu=$ii\n";
                                }
                        } 
                        # see on põhikordus
                        $veel = TRUE;
                        $aeg = $null_time;
                        ##tunnifailide kaupa see while kordus
                        $ii = 0;
                        $jrk = 0;
                        $outarray = array();
                        echo "sisse loetud, hakkan arvutama=".date("d-F-Y H:i:s",strtotime("now"))."\n";
                        while ($veel) {
                                $fn = '';
                                foreach($file as $host => $filename) {
                                        #siin leiab min ajaga rea
                                        $jrk = $failid[$filename][jrk];
                                        if ( $jrk<$failid[$filename][max] ) {
                                                if ($failid[$filename][time][$jrk] > $null_time and $aeg >= $failid[$filename][time][$jrk]) {
                                                        $aeg = $failid[$filename][time][$jrk];
                                                        $fn = $filename;
                                                }
                                                else {
                                                        if ($failid[$filename][time][$jrk] > $null_time and $aeg == $null_time) {
                                                                $aeg = $failid[$filename][time][$jrk];
                                                                $fn = $filename;
                                                        }
                                                }
                                        }
                                }
                                $jrk = $failid[$fn][jrk];
				#paneme aja järgi sorteeritud read uude massiivi outarray
                                if ( $jrk<$failid[$fn][max] ) {
                                        $outarray[] = $failid[$fn][line][$jrk]."";
#                                        if ( $summa < count($outarray) )
#                                                echo "hetke fail=$fn ja rea nr=$jrk ja selel faili max ridade arv=".$failid[$fn][max]."outarray size=".count($outarray)." \n";
                                }
                                if ( $jrk<$failid[$fn][max]) {
                                        $jrk++;
                                        $failid[$fn][jrk] = $jrk;
                                        if ($failid[$fn][time][$jrk] <= $null_time) {
                                                $failid[$fn][time][$jrk] = $aeg;
                                                $failid[$fn][line][$jrk] = print_time($aeg).'|'.$failid[$fn][line][$jrk];
                                        }
                                        $aeg = $failid[$fn][time][$jrk];
                                }
                                else {
                                        $failid[$fn][line][$jrk] = "END_OF_FILE";
                                        $failid[$fn][time][$jrk] = $null_time;
                                        $aeg=$null_time;
                                }
                                $veel = FALSE;
				#kontroll, kas leidub veel töötlemata ridu mõnes sisendarrays
                                foreach($file as $host => $filename) {
                                        $ii = $failid[$filename][jrk];
                                        if ( $ii<$failid[$filename][max]) {
                                                $veel = TRUE;
                                                break;
                                        }
                                }
                        }
                        #salvestama 1tunni read kokku liidetavasse faili outarrayst
                        echo "arvutamine lõpetatud, salvestan faili=".date("d-F-Y H:i:s",strtotime("now"))."\n";
                        $ii=0;
                        foreach( $outarray as $line ) {
                                fwrite($outfile, $line."\r\n");
                                $ii++;
                        }
			fclose($outfile);
                        unset($outarray);
                        unset($failid);
                        echo "faili salvestamine lõpetatud=".date("d-F-Y H:i:s",strtotime("now"))." -> ridu kokku=$ii\n";
                        $result = exec("gzip -f ".$config->config_values[paths][archive_path].make_dir($paev).$rakendus.".log.".$paev, $out);
			//$rakendus.".log.".$paev, $out);
                        echo "\nFail sai kokku pakitud käsuga: gzip -f ".$config->config_values[paths][archive_path].$rakendus.".log.".$paev."\n";

                }
        } # üle tundide foreach, järgmise tunni failid tööylemiseks kui on veel
        #siin peaks sulgema tulemusfaili, et gzippida
        if ($outfile) {
                @fclose($outfile);
        }
                #`gzip -f $outdir/$k.$date`
//$result = exec("gzip -f ".$config->config_values[paths][archive_path].$rakendus.".log.".$paev, $out);
                #if (empty($result)) {
                #echo "*********************************\n";
                #       echo "Viga arhiveerimisel => gzip -f ".$config->config_values[paths][temp_path].$rakendus.".log.".$paev."\n";
                #        return;
                #       echo "result = $result \n";
                #} else {
//                        echo "\nFail sai kokku pakitud käsuga: gzip -f ".$config->config_values[paths][archive_path].$rakendus.".log.".$paev."\n";
                #}
}


#vana funktsioon ajutiselt veel alles, aeglasem 2x. hiljem võib kustutada kui uus töötab ok
function arhiveeri1($files, $rakendus, $paev, $config) {
	#avatakse tulemusfail
	$outfile = @fopen($config->config_values[paths][archive_path].$rakendus.".log.".$paev, "w");
	$write_bytes = 33554432; #32Mb
	stream_set_write_buffer($outfile , $write_bytes );
        //$read_bytes =  16777216; #16Mb
	$read_bytes = 1048576;
	$null_time = null_time();
	
	#käiakse üle tunnid kuni 23ni> või kuni ridade massiivis veel ridu
	if (is_one_node($files)) {
		echo "Kasutuses üks node, tunnifailid võib lihtsalt kokku liita \n";
		merge_files($files, $config, $rakendus, $paev);
	} else {
		echo "Kasutuses palju nodesid, arvutame kokku, mergeme \n";
	foreach($files as $tund => $file ) {
		echo "tund= $tund , ";
		#avatakse kõik failid ja loetakse esimene rida mällu
		foreach($file as $host => $filename) {		
			$failid[$filename][handle] = @fopen($config->config_values[paths][processed_path].$filename, "r");
			if ($failid[$filename][handle]) {
				if (!feof($failid[$filename][handle])) {
					#$failid[$filename][line] = fgets($failid[$filename][handle], $read_bytes);
					$failid[$filename][line] = stream_get_line($failid[$filename][handle], $read_bytes, "\n");
					$failid[$filename][time] = find_time_from_line($failid[$filename][line], $paev);
    				}
			}
		} #yle ühetunni failide foreach, avame failid ja loeme esimesed read sisse

		# see on põhikordus
		$veel = TRUE;
		$aeg = null_time();
		while ($veel) {
			$fn = '';
			foreach($file as $host => $filename) {
				#siin leiab min ajaga rea
				if ($failid[$filename][line] != 'END_OF_FILE') {
                                        if ($failid[$filename][time] > $null_time and $aeg >= $failid[$filename][time]) {
                                                $aeg = $failid[$filename][time];
                                                $fn = $filename;
                                        } 
					else
	                        	if ($failid[$filename][time] > $null_time and $aeg == $null_time) {
        	                        	$aeg = $failid[$filename][time];
						$fn = $filename;
                	                } 
				}
			}
			# salvestame rea output faili
			$failid[$fn][line] = get_host($failid[$fn][line], $fn);
			fwrite($outfile, $failid[$fn][line]."\r\n");

			if (!feof($failid[$fn][handle])) {
				#$failid[$fn][line] = fgets($failid[$fn][handle], $read_bytes);
				$failid[$fn][line] = stream_get_line($failid[$fn][handle], $read_bytes, "\n"); 
				$failid[$fn][time] = find_time_from_line($failid[$fn][line], $paev);
				if ($failid[$fn][time] <= $null_time) {
					$failid[$fn][time] = $aeg;
					$failid[$fn][line] = print_time($aeg).'|'.$failid[$fn][line];
				}
				$aeg = $failid[$fn][time];
			} else {
	                        $failid[$fn][line] = "END_OF_FILE";
                                $failid[$fn][time] = $null_time;
				$aeg=$null_time;
			}
			$veel = FALSE;
			foreach($file as $host => $filename) {
				if (!feof($failid[$filename][handle])) {
					$veel = TRUE;
					break;
				}		
			}
		} 
		#see on failide sulgemise foreach
                foreach($file as $host => $filename) {
                        if ($failid[$filename][handle]) {
                        	fclose($failid[$filename][handle]);
                        }
                } # üle tunnifailide, faili handle sulgemised
	} # üle tundide foreach
	#siin peaks sulgema tulemusfaili, et gzippida
	}
        if ($outfile) {
	        fclose($outfile);
	}
		#`gzip -f $outdir/$k.$date`
		$result = exec("gzip -f ".$config->config_values[paths][archive_path].$rakendus.".log.".$paev, $out);
	        #if (empty($result)) {
        	#echo "*********************************\n";
                #	echo "Viga arhiveerimisel => gzip -f ".$config->config_values[paths][temp_path].$rakendus.".log.".$paev."\n";
	        #        return;
        	#       echo "result = $result \n";
	        #} else {
			echo "\nFail sai kokku pakitud käsuga: gzip -f ".$config->config_values[paths][archive_path].$rakendus.".log.".$paev."\n";
		#}
        #}

}	                        

function get_host($line, $fn) {
	$host = substr($fn, 0, strpos($fn, '-'));
	if (strpos($line, $host)<=0) {
		if (strlen($line) > 9) {
			if (substr_count($line, '|') >1) {
				$line = substr($line, 0, 9).$host.substr($line, 9);
			} else {
				$line = substr($line, 0, 9).$host.'|||'.substr($line, 9);
			}
		} else {
			$line = substr($line, 0, 9).$host."|";
		}
	}
	return $line;
}

function find_time_from_line($str, $paev) {
	#echo "strpos= ".strpos($str, '|')."\n";
	if (strpos($str, '|')<=0 and strpos($str, '|') !=8) {
		return null_time();
	}
	$aeg = strtotime($paev." ".substr($str, 0, 8));
	return $aeg;
}

function find_time($str, $paev) {
        if (strlen($str) != 8 ) {
                return null_time();
        }
	$aeg = strtotime($paev." ".substr($str, 0, 8));
        return $aeg;
}

function print_date($str) {
	return date("d-m-Y H:i:s",$str);
}

function print_time($str) {
        return date("H:i:s",$str);
}

function null_time() {
	return strtotime("2005-02-01 00:00:00");
}

function get_files_from_path($path) {
	$open_path = opendir($path);
	while (($file = readdir($open_path)) !== false) {
		if (is_file($path.'/'.$file)) {
			$name = explode('.', $file);
			$rak = explode('-',$name[0]);
			$rakendus = $rak[1];
			$paev = substr($name[2], 0, 10);
			$tund = substr($name[2], 11, 2);
			$host = $rak[0];
			$hour_files = $files[$rakendus][$paev];
			$hour_files[$tund][$host] = $file;
   		      	$files[$rakendus][$paev] = $hour_files;
	        }
	}
	closedir($open_path);
	return $files;
}


function is_one_node($files) {
	$node='';
	$count = 0;
	foreach($files as $tund => $file ) {
                foreach($file as $host => $filename) {
			if($host != $node) {
				$node = $host;
				$count = $count + 1;
				if ($count > 1) return FALSE;
			}
		}
	}
	if (count > 1) {
		return FALSE;
	}
	return TRUE;
}


function merge_files($files, $config, $rakendus, $paev) {
	#cat yks.txt kaks.txt kolm.txt > tulemus.txt
	//$command = "cat ";
	foreach($files as $tund => $file ) {
                foreach($file as $host => $filename) {
                        if($host != $node) {
                                //$command.=$config->config_values[paths][processed_path].$filename." ";
				copy($config->config_values[paths][processed_path].$filename, $config->config_values[paths][archive_path].make_dir($paev).$rakendus.".log.".$paev, "w");
				$result = exec("gzip -f ".$config->config_values[paths][archive_path].make_dir($paev).$rakendus.".log.".$paev, $out);
				#$rakendus.".log.".$paev, $out);
                        }
                }
        }
	//$command.="> ".$config->config_values[paths][archive_path].$rakendus.".log.".$paev;
	echo "Käsk= $resuult\n";
	//exec($command);
}


?>
