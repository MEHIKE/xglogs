

#rm error.log
ttt=""
fail=/app/xgate/wily/EPAgent/error.lock

#`/usr/bin/wget -r -o error.log -nc --tries=1 --spider http://kreedowli2.emt.ee:7003/web_krk/krkmain`
#ttt=ss`/bin/grep '200 OK' error.log`

#/bin/echo "1.kord="$ttt

count=0

/bin/date > /app/xgate/wily/EPAgent/aeg.txt
aeg=ss`/bin/grep Mon /app/xgate/wily/EPAgent/aeg.txt`
if [ "$aeg" != ss ] ;
then
	aeg=ss`/bin/grep ' 05:' /app/xgate/wily/EPAgent/aeg.txt`
	if [ "$aeg" != ss ] ;
	then
		exit 1
	fi
fi

while [ "$ttt" != ss ]
do
	#rm error.log
	`/usr/bin/wget -r -o /app/xgate/wily/EPAgent/error.log -nc --tries=1 --timeout=15 --spider http://pl-kreedowli1.emt.ee:8003/web_krk/krkmain`
	ttt=ss`/bin/grep '200 OK' /app/xgate/wily/EPAgent/error.log`

  #echo "$n * $i = `expr $i \* $n`"
  count=`expr $count + 1`
  #/bin/echo "count="$count
  if [ "$ttt" != ss ] ;
  then
	/bin/echo "Custom EPAgent|IsServerUp:status=0"
	/bin/echo "Custom EPAgent|IsServerUpMessage:message=200 OK"
	/bin/echo "<metric type='LongCounter' name='Custom EPAgent|IsServerUp:metricstatus' value='0'/>"
	#ttt="ss"
	rm -rf /app/xgate/wily/EPAgent/error.lock
	exit 1
  else 
	if [ "$count" == 3 ] ;
	then
		ttt=ss`/bin/grep 'Connection refused' /app/xgate/wily/EPAgent/error.log`
		if [ "$ttt" != ss ] ;
		then
                	/bin/echo "Custom EPAgent|IsServerUp:status=2"
			/bin/echo "Custom EPAgent|IsServerUpMessage:message=Connection refused"
                	/bin/echo "<metric type='LongCounter' name='Custom EPAgent|IsServerUp:metricstatus' value='2'/>"
                	#ttt="ss"
			exit 1
		else
                        /bin/echo "Custom EPAgent|IsServerUp:status=3"
                        /bin/echo "Custom EPAgent|IsServerUpMessage:message=Ei suuda serveriga ühendust luua"
                        /bin/echo "<metric type='LongCounter' name='Custom EPAgent|IsServerUp:metricstatus' value='3'/>"
                        #ttt="ss"
                        exit 1
		fi
	else
		if [ -e $fail ]
		then
			ttt='';
		else
                       /bin/echo "Custom EPAgent|IsServerUp:status=1"
                        /bin/echo "Custom EPAgent|IsServerUpMessage:message=Ei suutnud serveriga ühendust luua, katse="$count
                        /bin/echo "<metric type='LongCounter' name='Custom EPAgent|IsServerUp:metricstatus' value='1'/>"
			/bin/sleep 20s
			cp /app/xgate/wily/EPAgent/error.log /app/xgate/wily/EPAgent/error.lock
			ttt=""
		fi
	fi
  fi
#echo "ttt="$ttt
done

