
PID4=$(ps waux | grep "biglog.php" | grep -v "grep" | awk '{ print $2; exit; }')

if [ `expr $PID4 + 0` -eq 0 ] ; then
        #/bin/echo "PID=`expr $PID4 + 0`"
        /home/webapp/phplogs/bin_suur/biglog.php
else
	/bin/echo ""
        /bin/echo "*************************************************"
        /bin/echo "PID="$PID4
        /bin/echo "Protsess juba käib - biglog.php: "
        /bin/echo `date`
        /bin/echo "Seekord ei käivita.. järgmine kord proovib uuesti"
        /bin/echo "*************************************************"
fi


