
mem=""

#mem=ss`/bin/grep 'feof():' /home/webapp/phplogs/bin/logs/copylogs.log`ss
PID4=$(ps waux | grep "copylogs.php" | grep -v "grep" | awk '{ print $2; exit; }')
PID4a=$(ps -fu webapp | grep "copylogs.php" | grep -v "grep" | awk '{ print $2; exit; }')
PID4b=$(ps -fu webapp | grep "copylogs.php" | grep -v "grep" | awk '{ print $3; exit; }')

STARDITUD=$(ps waux | grep "copylogs.php" | grep -v "grep" | awk '{ print $9; exit; }')
HETKEL=$(date | awk '{ print $4; exit; }')

dat1=$(date | awk '{ print $2; exit; }')
dat2=$(date | awk '{ print $3; exit; }')
dat3=$(date | awk '{ print $4; exit; }')

  echo "PID="$PID4" PID1="$PID4a" PID2="$PID4b
  echo "uus Faililaiend="$dat1"_"$dat2"_"$dat3

#mem=ss`/bin/grep 'OutOfMemory' /home/webapp/bin_wls9/test.txt`
#except=" OutOfMemoryError> ss"
#except2=" -XX:+HeapDumpOnOutOfMemoryErrori ss"


#/bin/echo "väärtus="$mem
#  >>/home/webapp/bin_wls9/outOfMemory1.log 2>&1

fail=/home/webapp/bin_wls9/kreedo.locked
#echo $fail
#mem= "*$mem*"

#echo $mem 
#> /home/webapp/bin_wls9/oo.txt
#echo "ff"

#if [ "$mem" != ssss ] 
#then
echo "algus /n"
	#echo "rida"$mem
	/bin/echo "Kastkestame protsessi: iganädalane copylogs.log faili gzippimine ja 0mine!" | /bin/mail mehike@sms.emt.ee
	/bin/echo "Katkestame protsessi: iganädalane copylogs.log faili gzippimine ja 0mine!" | /bin/mail Rynno.Ruul@emt.ee
	echo "jooksis kokku, killin protsessi"
  /bin/kill -9 $PID4a $PID4b
  echo "Muuvime faili=/home/webapp/phplogs/bin/logs/copylogs.log"
  echo " Failiks=/home/webapp/phplogs/bin/logs/copylogs.log."$dat1"_"$dat2"_"$dat3
  /bin/mv /home/webapp/phplogs/bin/logs/copylogs.log /home/webapp/phplogs/bin/logs/copylogs.log.$dat1"_"$dat2"_"$dat3
  /bin/gzip /home/webapp/phplogs/bin/logs/copylogs.log.$dat1"_"$dat2"_"$dat3
  #/bin/sleep 3s
  #/bin/kill -QUIT $PID4
  #/bin/sleep 3s
  #/bin/kill -QUIT $PID4

	echo "Väljun"
	exit 1
#fi


