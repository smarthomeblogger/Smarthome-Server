#!/usr/bin/python
# -*- coding: utf-8 -*-
  
import sqlite3 as lite	#Für die Datenbankverbindung
import datetime			#Um Datum und Uhrzeit zu ermitteln
import urllib			#Um die URL aufzurufen
import sys				#Um Kommandozeilenargumente zu lesen

#Um die IP-Adresse zu ermitteln
import socket
import fcntl
import struct

now = datetime.datetime.now()
  
con = lite.connect('/var/www/html/database/data.sqlite')
 
#IP-Adresse der Servers feststellen und zurückgeben
#Parameter: ifname - 'wlan0' falls per WLAN verbunden und 'eth0' falls per LAN verbunden
def get_ip_address(ifname):
    s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    return socket.inet_ntoa(fcntl.ioctl(
        s.fileno(),
        0x8915,  # SIOCGIFADDR
        struct.pack('256s', ifname[:15])
    )[20:24])
  
#Daten aus Datenbank laden
def getData():
  
    with con:    
        cur = con.cursor()    
        cur.execute("SELECT * FROM ZWAVE_SENSOREN")
  
        return cur.fetchall()
          
#Sensorwerte abfragen und Wert in Datenbank schreiben
#Parameter:
		#room: Raum, in dem der Wert gesucht werden soll
		#sensor: Art des zu suchenden Sensors
def saveSensorData(room, sensor, ip):
	#entsprechenden Sensorwert abfragen
	sensorwert = urllib.urlopen("http://"+ip+"/api.php?action=getsensordata&room="+room+"&value="+sensor).read()
	
	#Datum und Uhrzeit ermitteln
	uhrzeit = now.strftime("%d.%m.%y %H:00") #Datum und Uhrzeit im Format TT.MM.JJ HH:00
	
	#Daten in Datenbank schreiben
	cur = con.cursor()    
	cur.execute("INSERT INTO SENSOR_DATA (ROOM, VALUE, SENSORART, DATETIME) VALUES ('"+room+"','"+sensorwert+"','"+sensor+"','"+uhrzeit+"')")         
	con.commit()
    
#Für jeden Sensor in jedem Raum den Wert in die Datenbank schreiben
for data in getData():
	saveSensorData(data[0], data[1], get_ip_address(sys.argv[1])) #data[0] enthält den KEY des Raumes und data[1] die Art des Sensors