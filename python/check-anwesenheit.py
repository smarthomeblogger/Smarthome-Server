#!/usr/bin/python
# -*- coding: utf-8 -*-
  
import sqlite3 as lite
import sys
import time
import os
  
con = lite.connect('/var/www/html/database/data.sqlite')
  
#Daten aus Datenbank laden
def getData():
  
    with con:
        cur = con.cursor()    
        cur.execute("SELECT * FROM userdata")
  
        return cur.fetchall()
          
#IP Anpingen und Ergebnis in Datenbank schreiben        
def checkAnwesenheit(name, ip):
  
    if os.system("fping -t 250 "+ip) == 0:
        with con:
          
            #Ergebnis auf Kommandozeile ausgeben
            print name+": zuhause"
          
            cur = con.cursor()    
  
            #Nutzer zuhause: In Datenbank schreiben
            cur.execute("UPDATE userdata SET AT_HOME ='true' WHERE USERNAME=:name", {"name": name})         
            con.commit()
    else:
        with con:
          
            #Ergebnis auf Kommandozeile ausgeben
            print name+": nicht zuhause"
              
            cur = con.cursor()    
  
            #Nutzer nicht zuhause: Wert in Datenbank schreiben
            cur.execute("UPDATE userdata SET AT_HOME ='false' WHERE USERNAME=:name", {"name": name})        
            con.commit()
     
#Endlosschleife 
while True:
    #Alle Nutzer aus Datenbank laden und für jeden die Anwesenheit prüfen und abspeichern
    for data in getData():
        checkAnwesenheit(data[0], data[2]) #data[0] ist Nutzername und data[1] die IP-Adresse
    time.sleep(1)