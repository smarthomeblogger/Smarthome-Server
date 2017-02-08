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

now = datetime.datetime.now()

con = lite.connect('/var/www/html/database/data.sqlite')

#Nutzerdaten, da die API im weiteren Verlauf der Tutorials noch geschützt wird
username = 'client'
password = 'clientpassword'

#IP-Adresse des Servers feststellen und zurückgeben
#Parameter: ifname - 'wlan0' falls per WLAN verbunden und 'eth0' falls per LAN verbunden
def get_ip_address(ifname):
	s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
	return socket.inet_ntoa(fcntl.ioctl(
	s.fileno(),
	0x8915,
	struct.pack('256s', ifname[:15])
	)[20:24])
		
#Sensorwerte abfragen und Wert in Datenbank schreiben
#Parameter:
	#room: Raum, in dem der Wert gesucht werden soll
	#sensor: Art des zu suchenden Sensors
def saveSensorData(room, type, id, ip):
	#entsprechenden Sensorwert abfragen
	
	params = urllib.urlencode({'action': 'getsensordata', 'room': room, 'type': type, 'id': id, 'username': username, 'password': password})
	headers = {"Content-type": "application/x-www-form-urlencoded", "Accept": "text/plain"}
	conn = httplib.HTTPConnection(ip)
	conn.request("POST", "/api.php", params, headers)
	response = conn.getresponse()
	sensorwert = response.read()
	conn.close
	
	#Datum und Uhrzeit ermitteln
	uhrzeit = now.strftime("%Y-%m-%d %H:%M") #Datum und Uhrzeit im Format JJJJ-MM-TT HH:MM
	
	#Daten in Datenbank schreiben
	cur = con.cursor()
	cur.execute("INSERT INTO SENSOR_DATA (DEVICE_ID, DEVICE_TYPE, DATETIME, VALUE) VALUES ('"+id+"', '"+type+"', '"+uhrzeit+"', '"+sensorwert+"')")
	con.commit()
	
#Für jeden Sensor in jedem Raum den Wert in die Datenbank schreiben
#for data in getData():
#	saveSensorData(data[0], data[1], get_ip_address(sys.argv[1])) #data[0] enthält den KEY des Raumes und data[1] die Art des Sensors

with con:
	cur = con.cursor()
	cur.execute("SELECT * FROM ZWAVE_SENSOREN")
	
	for data in cur.fetchall():
		if(data[4] == "true"): #data[4] legt fest, ob der Sensorwerteverlauf dieses Sensors gespeichert werden soll
			saveSensorData(data[0], "Z-Wave Sensor", data[1], get_ip_address(sys.argv[1])) #data[0] enthält den KEY des Raumes und data[1] die ID des Sensors
