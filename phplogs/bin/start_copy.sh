
PID4=$(ps waux | grep "copylogs.php" | grep -v "grep" | awk '{ print $2; exit; }')

if [ `expr $PID4 + 0` -eq 0 ] ; then
        #/bin/echo "PID=`expr $PID4 + 0`"
        /home/webapp/phplogs/bin/copylogs.php
else
	/bin/echo ""
        /bin/echo "*************************************************"
        /bin/echo "PID="$PID4
        /bin/echo "Protsess juba k�ib - copylogs.php: "
        /bin/echo `date`
        /bin/echo "Seekord ei k�ivita.. j�rgmine kord proovib uuesti"
        /bin/echo "*************************************************"
fi


