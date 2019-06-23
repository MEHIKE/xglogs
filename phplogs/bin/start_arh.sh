
PID4=$(ps waux | grep "arhiveeri.php" | grep -v "grep" | awk '{ print $2; exit; }')

if [ `expr $PID4 + 0` -eq 0 ] ; then
        #/bin/echo "PID=`expr $PID4 + 0`"
        /home/webapp/phplogs/bin/arhiveeri.php
else
	/home/webapp/phplogs/bin/send_errormail.php
	/bin/echo ""
        /bin/echo "*************************************************"
        /bin/echo "PID="$PID4
        /bin/echo "Protsess juba käib: "
        /bin/echo `date`
        /bin/echo "Seekord ei käivita.. järgmine kord proovib uuesti"
        /bin/echo "*************************************************"
fi


