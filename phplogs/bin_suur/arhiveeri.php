#!/usr/bin/php -q
<?php

include ('/home/webapp/phplogs/bin/config.class.php');

ini_set("memory_limit","2000M");
#ini_set("memory_limit","-1");

$config = config::getInstance();

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
			if (to_old($paev, $paev1)) {
				arhiveeri($hour_files, $rakendus, $paev, $config);
			}
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

function make_dir($paev) {
        $dir = explode('-', $paev);
        return $dir[0].'/'.$dir[1].'/'.$dir[2].'/';
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
	//if (strtotime($paev) <= $vanapaev ) {
	if (to_old($paev, $vanapaev)) {
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

function delete_old_file($file, $rakendus, $paev, $vanapaev, $config) {
        //if (strtotime($paev) <= $vanapaev ) {
        if (to_old($paev, $vanapaev)) {
                echo "\n\nVanemate failide kustutamine--->\n";
                //foreach($files as $tund => $file ) {
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
                //}
        }
}


function arhiveeri($files, $rakendus, $paev, $config) {
        #avatakse tulemusfail
        //$outfile = @fopen($config->config_values[paths][archive_path].$rakendus.".log.".$paev, "w");
        $write_bytes = 33554432; #32Mb
        #$write_bytes = 67108864; #64M

        //stream_set_write_buffer($outfile , $write_bytes );

        //$read_bytes =  16777216; #16Mb
	if (!is_dir($config->config_values[paths][archive_path].make_dir($paev))) {
		@mkdir ($config->config_values[paths][archive_path].make_dir($paev), 0755, true);
	}
        $read_bytes = 1048576;
	$null_time = null_time();
        #$read_bytes =  67108864; #64M
        #$read_bytes = 33554432;

        #$read_bytes = 16777216; #16Mb
        #$read_bytes = 1048576;
        #$read_bytes = 134217728;

        #käiakse üle tunnid kuni 23ni> või kuni ridade massiivis veel ridu
        if (is_one_node($files)) {
                echo "Kasutuses üks node, tunnifailid võib lihtsalt kokku liita \n";
                merge_files($files, $config, $rakendus, $paev);
        } else {
                echo "Kasutuses palju nodesid, arvutame kokku, mergeme \n";

	        #käiakse üle ühe rakenduse tunnifailid. kui kasvõi ühe maht on suurem lubatavast, siis liidame lihtsalt kokku, cat kasuga
        	if (is_warning_size($files, $config) or is_mdp($rakendus) or is_elly($rakendus) or is_wally($rakendus)) {
                	echo "Vähemalt ühe logifaili suurus liiga suur (ületab mälumahu), tunnifailid liidame lihtsalt kokku (cat) \n";
	                merge_big_files($files, $config, $rakendus, $paev);
			#exit;
        	} else {
                	echo "Failisuurused on lubatud piiris, arvutame kokku, mergeme \n";

#                if (is_mdp($rakendus)) {
#                        echo "Vähemalt ühe logifaili suurus liiga suur (ületab mälumahu), tunnifailid liidame lihtsalt kokku (cat) \n";
#                        merge_big_files($files, $config, $rakendus, $paev);
                        #exit;
#                } else {
#                        echo "Failisuurused on lubatud piiris, arvutame kokku, mergeme \n";

                #käiakse üle ühe rakenduse tunnifailide suuruste summa. kui kasvõi ühe tunni maht on suurem lubatavast, siis liidame lihtsalt kokku, cat kasuga
                if (is_warning_size_sum($files, $config)) {
                        echo "Vähemalt ühe tunni logifailide suuruste summa on liiga suur (ületab mälumahu), tunnifailid liidame lihtsalt kokku (cat) \n";
                        merge_big_files($files, $config, $rakendus, $paev);
                        #exit;
                } else {
                        echo "Failisuurused on lubatud piiris, arvutame kokku, mergeme \n";


	        foreach($files as $tund => $file ) {
			echo "tunni algusaeg=".date("d-F-Y H:i:s",strtotime("now"))."\n";
        	        echo "tund= $tund , \n";

		        $outfile = @fopen($config->config_values[paths][archive_path].make_dir($paev).$rakendus.".log.".$paev.'-'.$tund, "w");
		        #$write_bytes = 33554432; #32Mb
			$write_bytes = 1048576;
		        stream_set_write_buffer($outfile , $write_bytes );

			#loetakse järjest tunnifaile - hostid võetakse sisemises korduses
                	#avatakse kõik failid ja loetakse esimene rida mällu
			$summa=0;
	                foreach($file as $host => $filename) {

				
			if (is_file($config->config_values[paths][processed_path].$filename)) {

				$file_size=filesize($config->config_values[paths][processed_path].$filename);
				echo "failisuurus $filename=$file_size \n";
				echo "failisuurus uus: ".formatBytes($file_size)."Mb \n";

				#kui failisuurus on väiksem, siis ilmselt tühifail
				if ($file_size < 5) {
					echo "Failisuurus on mõtetult väike, kustutame faili \n";
					unlink($config->config_values[paths][processed_path].$filename);
					continue;
				}
        	                $failid[$filename][handle] = @fopen($config->config_values[paths][processed_path].$filename, "r");
                	        if ($failid[$filename][handle]) {
	                                #if (!feof($failid[$filename][handle])) {
                                        #$failid[$filename][line] = fgets($failid[$filename][handle], $read_bytes);
					#siin peaks lugema kogu faili korraga masiividesse
					#hetkel avatakse lihtsalt failid ja loetakse esimesed read sisse
					#ja hiljem eraldi iga rida - odavam oleks korraga mällu lugeda
					#--------odavam kasutada explode funktsiooni? kuupäevad on juba õiged kopeerimise scriptist. esialgu võib jääda
					$ii = 0;
					while (!feof($failid[$filename][handle])) {
                        	              $failid[$filename][line][$ii] = stream_get_line($failid[$filename][handle], $read_bytes, "\r\n");
                                	      $failid[$filename][time][$ii] = find_time_from_line($failid[$filename][line][$ii], $paev);
						#echo "ii=$ii reaaeg=".date("d-F-Y H:i:s",$failid[$filename][time][$ii])." \n";
					##$failid[$filename][jrk] = 0;
					      $ii++;
        	                        }
					#echo "Filename=$filename ridu=$ii \n";
					fclose($failid[$filename][handle]);
                        	        $failid[$filename][line][$ii] = "END_OF_FILE";
                                	$failid[$filename][time][$ii] = $null_time;
					$failid[$filename][jrk] = 0;
					$failid[$filename][max] = $ii;
					$summa = $summa + $ii;
					echo "file=$filename ja ridu=$ii\n";
        	                }
				#} else {
				#	echo "faili suurus (".$file_size." e. ".formatBytes($file_size).") on suurem kui lubatav =  -> ".$config->config_values[paths][processed_path].$filename." \n";
				#	echo "Kasutan antud tüüpi failide liitmisel teist meetodit (liidame kokku lihtsalt [cat])! ".$host." \n";
					#exit;
				#}
			}

                	} #yle ühetunni failide foreach, avame failid ja loeme esimesed read sisse
			
	                # see on põhikordus
        	        $veel = TRUE;
                	$aeg = $null_time;
			##tunnifailide kaupa see while kordus
			##out võiks olla massiivi ja alles pärast while korduse
			##lõppu võiks salvestada out kirjed faili. 
			$ii = 0;
			$jrk = 0;
			$outarray = array();
			echo "sisse loetud, hakkan arvutama=".date("d-F-Y H:i:s",strtotime("now"))."\n";
			if ($summa > 0) {
	                while ($veel) {
        	                $fn = '';
                        	foreach($file as $host => $filename) {
                	        	#siin leiab min ajaga rea
					$jrk = $failid[$filename][jrk];
#                	                echo "jrk=$jrk filename=$filename time=".$failid[$filename][time][$jrk]." aeg=$aeg \n";
##					if ($failid[$filename][line][$jrk] != 'END_OF_FILE' && $jrk<$failid[$filename][max] ) {
					if ( $jrk<$failid[$filename][max] ) {
#						echo "jrk=$jrk filename=$filename time=".$failid[$filename][time][$jrk]." nulltime=".null_time()." aeg=$aeg \n";
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
            		        # salvestame rea output faili
				$jrk = $failid[$fn][jrk];
##        	                $failid[$fn][line][$jrk] = get_host($failid[$fn][line][$jrk], $fn);
	
				#ei panda siin faili vaid alles pärast while kordust korraga read arrayst
                	        #fwrite($outfile, $failid[$fn][line]."\r\n")
##				if ($failid[$fn][line][$jrk] != 'END_OF_FILE' && $jrk<$failid[$fn][max] )
				if ( $jrk<$failid[$fn][max] ) {
					#kui läheb suurus üle 1Gb, sisi kirjutab faili vahepeal
					$suurus = memory_get_usage();
					if ($suurus >= 1920000000) {
						#2097152000 
						echo "output suurus läks liiga suureks, kirjutan tulemuse vahepeal faili = $suurus\n";
			                        foreach( $outarray as $line ) {
                        				fwrite($outfile, $line."\r\n");
			                                $ii++;
                			        }
	                       			unset($outarray);
						$outarray = NULL;
						$outarray = array();
					}
					$outarray[] = $failid[$fn][line][$jrk]."";
					
					if ( $summa < count($outarray) )
						echo "hetke fail=$fn ja rea nr=$jrk ja selel faili max ridade arv=".$failid[$fn][max]."outarray size=".count($outarray)." \n";
					#array_shift($failid[$fn][line]);
					#array_shift($failid[$fn][time]);
				}
				#------------------ei loeta failist vaid massiividest
        	                #if (!feof($failid[$fn][handle])) {
##				if ( $failid[$fn][line][$jrk] != 'END_OF_FILE' && $jrk<$failid[$fn][max]) {
				if ( $jrk<$failid[$fn][max]) {
					$jrk++;
	                                #$failid[$fn][line] = fgets($failid[$fn][handle], $read_bytes);
        	                        #$failid[$fn][line] = stream_get_line($failid[$fn][handle], $read_bytes, "\n");
##                	                $failid[$fn][time][$jrk] = find_time_from_line($failid[$fn][line][$jrk], $paev);
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
                        	foreach($file as $host => $filename) {
	                                #if (!feof($failid[$filename][handle])) {
					$ii = $failid[$filename][jrk];
##					if ( $failid[$filename][line][$ii] != 'END_OF_FILE' && $ii<$failid[$filename][max]) {
					if ( $ii<$failid[$filename][max]) {
                        	                $veel = TRUE;
                                	        break;
	                                }
        	                }
	                }
			#siin paneme asjad outfaili lõppu
			echo "arvutamine lõpetatud, salvestan faili=".date("d-F-Y H:i:s",strtotime("now"))."\n";
			$ii=0;
			foreach( $outarray as $line ) {
				fwrite($outfile, $line."\r\n");
				$ii++;
			}
			}
			fclose($outfile);
			unset($outarray);
			unset($failid);
			echo "faili salvestamine lõpetatud=".date("d-F-Y H:i:s",strtotime("now"))." -> ridu kokku=$ii\n";

#	delete_old_file($file, $rakendus, $paev, $paev1, $config);

                        $result = exec("gzip -f ".$config->config_values[paths][archive_path].make_dir($paev).$rakendus.".log.".$paev.'-'.$tund, $out);
                        //$rakendus.".log.".$paev, $out);
                        echo "\nFail sai kokku pakitud käsuga: gzip -f ".$config->config_values[paths][archive_path].make_dir($paev).$rakendus.".log.".$paev.'-'.$tund."\n";

			#--------------------kas vaja ajutised arrayd ka unsettida? $failid

        	        #see on failide sulgemise foreach
            		##foreach($file as $host => $filename) {
	                ##        if ($failid[$filename][handle]) {
        	        ##                fclose($failid[$filename][handle]);
                	##        }
		}}
 # üle tunnifailide, faili handle sulgemised
        } # üle tundide foreach
        #siin peaks sulgema tulemusfaili, et gzippida
        }
        if ($outfile) {
                @fclose($outfile);
        }
                #`gzip -f $outdir/$k.$date`
//                $result = exec("gzip -f ".$config->config_values[paths][archive_path].$rakendus.".log.".$paev, $out);
                #if (empty($result)) {
                #echo "*********************************\n";
                #       echo "Viga arhiveerimisel => gzip -f ".$config->config_values[paths][temp_path].$rakendus.".log.".$paev."\n";
                #        return;
                #       echo "result = $result \n";
                #} else {
//                        echo "\nFail sai kokku pakitud käsuga: gzip -f ".$config->config_values[paths][archive_path].$rakendus.".log.".$paev."\n";
                #}
        //}
}


function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision);
}


function arhiveeri1($files, $rakendus, $paev, $config) {
	#avatakse tulemusfail
	$outfile = @fopen($config->config_values[paths][archive_path].$rakendus.".log.".$paev, "w");
	#$write_bytes = 33554432; #32Mb
	#$write_bytes = 67108864; #64M

	#stream_set_write_buffer($outfile , $write_bytes );

        #$read_bytes =  16777216; #16Mb
        #$read_bytes =  67108864; #64M
	#$read_bytes = 33554432;

	#$read_bytes = 16777216; #16Mb
	#$read_bytes = 1048576;
	#$read_bytes = 134217728;

	#käiakse üle tunnid kuni 23ni> või kuni ridade massiivis veel ridu
	if (is_one_node($files)) {
		echo "Kasutuses üks node, tunnifailid võib lihtsalt kokku liita \n";
		merge_files($files, $config, $rakendus, $paev);
	} else {
		echo "Kasutuses palju nodesid, arvutame kokku, mergeme \n";
		$command = "cat ";
		foreach($files as $tund => $file ) {
			echo "tund= $tund , ";
			#avatakse kõik failid ja loetakse esimene rida mällu
			foreach($file as $host => $filename) {		
#				$failid[$filename][handle] = @fopen($config->config_values[paths][processed_path].$filename, "r");
                        if($host != $node) {
                                $command.=$config->config_values[paths][processed_path].$filename." ";
                        }


			} #yle ühetunni failide foreach, avame failid ja loeme esimesed read sisse
			# see on põhikordus
		} # üle tundide foreach
        $command.="| sort --key=1,9 > ".$config->config_values[paths][archive_path].$rakendus.".log.".$paev;
        echo "\nKäsk= $command \n";
        exec($command, $out);

	#siin peaks sulgema tulemusfaili, et gzippida
	}
	#`gzip -f $outdir/$k.$date`
	$result = exec("gzip -f ".$config->config_values[paths][archive_path].$rakendus.".log.".$paev, $out);
	echo "\nFail sai kokku pakitud käsuga: gzip -f ".$config->config_values[paths][archive_path].$rakendus.".log.".$paev."\n";

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
	#$reaaeg=explode('|', $str);
	if (strpos($str, '|')<=0 and strpos($str, '|') !=8) {
		return null_time();
	}
#	if ( $aeg[0] 
	$aeg = strtotime($paev." ".substr($str, 0, 8));
	#echo "aaaaaaaa=$aeg > ".$reaaeg[0]." $str \n";
#	$aeg = strtotime($paev." ".$reaaeg[0]);
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


#kui faili on suurem kui warning size, siis TRUE, tavaliselt FALSE
function is_warning_size($files, $config) {
        $node='';
        $count = 0;
	$summ =0;
        foreach($files as $tund => $file ) {
                foreach($file as $host => $filename) {
			$file_size=filesize($config->config_values[paths][processed_path].$filename);
			$summ = $summ + $file_size;
                        echo "failisuurus $filename=".$file_size." fail=".$config->config_values[paths][processed_path].$filename."  \n";
                        #echo "failisuurus uus: ".formatBytes($file_size)."\n";
                        if ($file_size >= ($config->config_values[errors][max_filesize_warning_arh] ) ) {
			        #277200968
                	        #if($host != $node) {
        	                #$node = $host;
                                #$count = $count + 1;
                                #if ($count > 1) return FALSE;
				echo "failisuurus ületab warning piiri: ".$file_size." fail=".$config->config_values[paths][processed_path].$filename."  \n";
				return TRUE;
                        }
                }
        }
#	if ($summ >= ($config->config_values[errors][max_filesize_warning_arh])) {
#		echo "failisuuruste summa ületab warning piiri: ".$summ." >= ".$config->config_values[errors][max_filesize_warning_arh]."  \n";
#		return TRUE;
#	}
        #if (count > 1) {
        #        return FALSE;
        #}
        return FALSE;
}


#kui tunni failide summa on suurem kui warning size, siis TRUE, tavaliselt FALSE
function is_warning_size_sum($files, $config) {
        $node='';
        $count = 0;
        $summ =0;
        $sumtund ='';
        foreach($files as $tund => $file ) {
		#liidab kokku ainult ühe tunni failide summa
		if ($tund != $sumtund ) {
			$sumtund = $tund;
			$summ = 0;
		}
                foreach($file as $host => $filename) {
                        $file_size=filesize($config->config_values[paths][processed_path].$filename);
                        $summ = $summ + $file_size;
                        #echo "failisuurus $filename=".$file_size." fail=".$config->config_values[paths][processed_path].$filename."  \n";
                        #echo "failisuurus uus: ".formatBytes($file_size)."\n";
                        if ($summ >= ($config->config_values[errors][max_filesize_warning_arh_] ) ) {
                                                                                               #277200968
	                        #if($host != $node) {
                                #$node = $host;
                                #$count = $count + 1;
                                #if ($count > 1) return FALSE;
                                echo "tunni ".$tund." failisuuruste summa ületab warning piiri: ".$summ." faili juures=".$config->config_values[paths][processed_path].$filename."  \n";
                                return TRUE;
                        }
                }
		#}
        }
#        if ($summ >= ($config->config_values[errors][max_filesize_warning_arh])) {
#                echo "failisuuruste summa ületab warning piiri: ".$summ." >= ".$config->config_values[errors][max_filesize_warning_arh]."  \n";
#                return TRUE;
#        }
        #if (count > 1) {
        #        return FALSE;
        #}
        return FALSE;
}


function merge_files($files, $config, $rakendus, $paev) {
	#cat yks.txt kaks.txt kolm.txt > tulemus.txt
//	$command = "cat ";
	foreach($files as $tund => $file ) {
                foreach($file as $host => $filename) {
                        if($host != $node) {
//                                $command.=$config->config_values[paths][processed_path].$filename." ";
                                copy($config->config_values[paths][processed_path].$filename, $config->config_values[paths][archive_path].make_dir($paev).$rakendus.".log.".$paev.'-'.$tund);
                                $result = exec("gzip -f ".$config->config_values[paths][archive_path].make_dir($paev).$rakendus.".log.".$paev.'-'.$tund, $out);
//$rakendus.".log.".$paev, $out);

                        }
                }
        }
//	$command.="> ".$config->config_values[paths][archive_path].$rakendus.".log.".$paev;
	echo "Käsk= $result \n";
//	exec($command);
}


function merge_big_files($files, $config, $rakendus, $paev) {
	echo "merge big fails sisenevad muutujad: rakendus=".$rakendus." / paev=".$paev." / node=".$node." / make_dir=".make_dir($paev)." \n";
        #cat yks.txt kaks.txt kolm.txt > tulemus.txt
      	$command = "cat ";
        foreach($files as $tund => $file ) {
                foreach($file as $host => $filename) {
                        if($host != $node) {
				$command.=$config->config_values[paths][processed_path].$filename." ";
                                #copy($config->config_values[paths][processed_path].$filename, $config->config_values[paths][archive_path].make_dir($paev).$rakendus.".log.".$paev.'-'.$tund);
                                #$result = exec("gzip -f ".$config->config_values[paths][archive_path].make_dir($paev).$rakendus.".log.".$paev.'-'.$tund, $out);
				#$rakendus.".log.".$paev, $out);

                        }
                }
        }
	$command.="> ".$config->config_values[paths][archive_path].make_dir($paev).$rakendus.".log.".$paev.'-'.$tund;
        echo "Käsk= $command \n";
	exec($command, $out);
	$result = "gzip -f ".$config->config_values[paths][archive_path].make_dir($paev).$rakendus.".log.".$paev.'-'.$tund;
	echo "gzip käsk: ".$result." \n";
	exec($result, $out);
}


function is_mdp($rakendus) {
        $host = "mdp";
        if (strpos($rakendus, $host)>=0) {
		echo "is_mdp, rakendus= ".$rakendus." \n";
#		return true;
		return false;
        }
        return false;
}

function is_elly($rakendus) {
        $host = "elly";
        if (strpos($rakendus, $host)>=0) {
                echo "is_mdp, rakendus= ".$rakendus." \n";
#               return true;
                return false;
        }
        return false;
}

function is_wally($rakendus) {
        $host = "wally";
        if (strpos($rakendus, $host)>=0) {
                echo "is_mdp, rakendus= ".$rakendus." \n";
#               return true;
                return false;
        }
        return false;
}



function to_old($paev, $paev1) {
        //$aeg = substr($filename, strpos($filename, ".log." )+5, 10);
#        echo "aeg= $aeg \n";
#2009-02-02
#0123456789
        $file_time = mktime(0,0,0,substr($paev,5,2), substr($paev, 8,2), substr($paev, 0,4));

        #$mydate = date("Y-m-d-G", $file_time);
	#$mydate1 = date("Y-m-d-G", $paev1);
       # echo "paev failiaeg= $file_time ja vorreldav time= $paev1  failiaeg date(paev)= $mydate > võrreldav date(paev1)= $mydate1 \n";
        if ($file_time > $paev1) {
        #       echo "failiaeg aeg on suurem  paev>paev1 \n";
                return FALSE;
        }
#       else {
                return TRUE;
#       }
}

?>

