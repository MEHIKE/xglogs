
PID4=$(ps waux | grep "arhiveeri_tana.php" | grep -v "grep" | awk '{ print $2; exit; }')

if [ `expr $PID4 + 0` -eq 0 ] ; then
	#/bin/echo "PID=`expr $PID4 + 0`"
	/home/webapp/phplogs/bin/arhiveeri_tana.php
else
	/bin/echo "*************************************************"
        /bin/echo "PID="$PID4
	/bin/echo "Protsess juba käib: "
	/bin/echo `date`
	/bin/echo "Seekord ei käivita.. järgmine kord proovib uuesti"
	/bin/echo "*************************************************"
fi


