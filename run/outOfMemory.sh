
mem=""

mem=ss`/bin/grep 'OutOfMemory' /app/xgate/user_projects9/domains/kreedowli9preliveclusterdomain/servers/kreedoPreliveManagedServerA/logs/kreedoPreliveManagedServerA.out`

#fail=/home/webapp/bin_wls9/kreedo.locked

if [ "$mem" != ss ] 
then
 echo "Custom EPAgent|OutOfMemory:error=1"
 echo "<metric type='LongCounter' name='Custom EPAgent|OutOfMemory:metricerror' value='1'/>"
else
 echo "Custom EPAgent|OutOfMemory:error=0"
 echo "<metric type='LongCounter' name='Custom EPAgent|OutOfMemory:metricerror' value='0'/>"
fi

#echo "Custom EPAgent|Processes:process=Tuvastamata menetlus t??tab liiga kaua"
#echo "Custom EPAgent|Processes:tund=3.6"
#echo "Custom EPAgent|Processes:start_time=2005-10-02 10:11:12"
#echo "Custom EPAgent|Processes:tund1=3,6"


