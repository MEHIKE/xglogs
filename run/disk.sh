#!/bin/sh
#ketta andmete saatmine wilyle EPAgendiga

text1=""
text2=""
text3=""
column=0
size=0
used=0
pr=0


for ii in `df | grep /`
do
 column=$((column+1))
if [ $column == 2 ] ; then
  echo "Custom EPAgent|Diskspace:size="$((ii/1024/1024))
  size=`expr ${ii}`
fi

if [ $column == 3 ] ; then
  echo "Custom EPAgent|Diskspace:used="$((ii/1024/1024))
  used=`expr ${ii}`
fi

if [ $column == 4 ] ; then
  echo "Custom EPAgent|Diskspace:free="$((ii/1024/1024))
  #pr=`expr ${used}*100/${size}`
  pr=$((used*100/size))
  echo "Custom EPAgent|Diskspace:used%="$pr
fi

if [ $column == 6 ] ; then
  exit
fi
done

