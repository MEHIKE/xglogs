
PID4=$(ps waux | grep "check.php" | grep -v "grep" | awk '{ print $2; exit; }')

if [ `expr $PID4 + 0` -eq 0 ] ; then
        #/bin/echo "PID=`expr $PID4 + 0`"
        /home/webapp/phplogs/bin/check.php
else
	/bin/echo ""
        /bin/echo "*************************************************"
        /bin/echo "PID="$PID4
        /bin/echo "Protsess juba k�ib - check.php: "
        /bin/echo `date`
        /bin/echo "Seekord ei k�ivita.. j�rgmine kord proovib uuesti"
        /bin/echo "*************************************************"
fi


