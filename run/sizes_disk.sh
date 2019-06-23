#!/bin/bash

df -t ext -t ext2 -t ext3 -t ext4 -h | awk '
NR>1 {
                printf "Custom EPAgent|Disks|%s:TotalGB=%g\n", $6, $2;
                printf "Custom EPAgent|Disks|%s:UsedGB=%g\n", $6, $3;
                printf "Custom EPAgent|Disks|%s:AvailGB_=%g\n", $6, $4;
                printf "Custom EPAgent|Disks|%s:Used1%=%g\n", $6, $5;
}'

