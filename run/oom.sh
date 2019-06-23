#!/bin/sh
#ketta andmete saatmine wilyle EPAgendiga

fail=/home/webapp/bin_wls9/kreedo.locked
echo "Custom EPAgent|OutOfMemory:alive=1"

if [ -e $fail ]
then
 echo "Custom EPAgent|OutOfMemory:error=1"
 echo "<metric type='LongCounter' name='Custom EPAgent|OutOfMemory:metricerror' value='1'/>"
else
 echo "Custom EPAgent|OutOfMemory:error=0"
 echo "<metric type='LongCounter' name='Custom EPAgent|OutOfMemory:metricerror' value='0'/>"
fi

