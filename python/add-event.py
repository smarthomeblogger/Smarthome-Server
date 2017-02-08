#!/usr/bin/python
# -*- coding: utf-8 -*-

import sqlite3 as lite		#Für die Datenbankverbindung
import datetime				#Um Datum und Uhrzeit abzufragen
import urllib				#Um die URL aufzurufen
import httplib				#Um die URL aufzurufen
import sys					#Um Kommandozeilenargumente zu lesen

#Um die IP-Adresse zu ermitteln
import socket
import fcntl
import struct

now = datetime.datetime.now().strftime("%s")

con = lite.connect('/var/www/html/database/data.sqlite')
			
def addEvent(type, text):
	#Daten in Datenbank schreiben
	cur = con.cursor()
	cur.execute("INSERT INTO EVENTS (TIMESTAMP, TEXT, TYPE) VALUES ('"+now+"', '"+text+"', '"+type+"')")
	con.commit()

#Skriptaufruf: sudo python /var/www/html/python/add-event.py [TYP] [TEXT]

text = ""

if(len(sys.argv) > 2):
	for index in range(2, len(sys.argv)):
		if(index > 2):
			text = text+" "
		text = text+sys.argv[index]

addEvent(sys.argv[1], text)