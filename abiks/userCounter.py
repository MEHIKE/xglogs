#!/usr/bin/python

import mmap
import os
import re
import gzip
import logging
import datetime
import sys

reload(sys)
sys.setdefaultencoding('UTF8')

logging.basicConfig(format='%(asctime)s %(message)s', filename='userCounter.log', level=logging.DEBUG)

usersFileName = "unique_users2.txt"

if not os.path.exists(usersFileName):
	file = open(usersFileName, 'w')
	file.write("Active user\n")
	file.close()
		
		
days = 90
date_list = [(datetime.datetime.now() - datetime.timedelta(days=x)).date() for x in range(0, days)]
logging.info("Starting to search unique users from log files")

for date in date_list:
	directories = ["/archive/" + str(date.year) + "/" + str(date.strftime('%m')) + "/" + str(date.strftime('%d')), "/logs/arhiiv/" + str(date.year) + "/" + str(date.strftime('%m')) + "/" + str(date.strftime('%d'))]
	for directory in directories:
		if not os.path.exists(directory):
			continue
		logging.info("Searching dkprod logs from directory %s", directory)
		for filename in os.listdir(directory):
			if filename.startswith("dkprod"):
				logging.info("Searching users from file %s", filename)
				logsfile = gzip.open(directory + "/" + filename, "r")
				numberOfUniqueUsersFromFile = 0
				for line in logsfile: 
					if "user-teliadk-" in line.lower():
						userId = re.search("user-teliadk-([1-9][0-9]*)", line, re.IGNORECASE)
						if userId :
							userId = userId.group().lower()
							fileRead = open(usersFileName, 'rb', 0)
							s = mmap.mmap(fileRead.fileno(), os.stat(usersFileName).st_size, access=mmap.ACCESS_READ)
							if s.find(str.encode(userId)) == -1:
								fileWrite = open(usersFileName, 'a')
								fileWrite.write(userId + "\n")
								fileWrite.close()
								numberOfUniqueUsersFromFile+=1
							fileRead.close()
				logging.info("Found %d unique users from file", numberOfUniqueUsersFromFile)
				logsfile.close()
