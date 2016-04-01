<?php

$room = $_GET['room']; //Der Raum, in dem sich die Steckdose befindet
$device = $_GET['device']; //Das zu schaltende Ger�t
$zustand = $_GET['zustand']; //Gew�nschter Zustand [1 f�r an; 0 f�r aus]

//Datenbankverbindung herstellen
$SQLITEdb = "database/data.sqlite";
$db = new PDO("sqlite:".$SQLITEdb);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


//Hauscode und Steckdosennummer aus Datenbank laden
$query = $db->query("SELECT * FROM 'funksteckdosen' WHERE ROOM == '".$room."' AND DEVICE == '".$device."'");

//Abfrageergebnisse speichern
if($result = $query->fetch(PDO::FETCH_ASSOC)){
	$hauscode = $result['HAUSCODE'];
	$steckdosennummer = $result['STECKDOSENNUMMER'];
}

//Schaltbefehl f�r Steckdosen
shell_exec("/usr/local/bin/send  ".$hauscode." ".$steckdosennummer." ".$zustand);

//Status der geschalteten Steckdose aktualisieren
$query = $db->exec("UPDATE 'funksteckdosen' SET 'ZUSTAND' = '".$zustand."' WHERE ROOM == '".$room."' AND DEVICE == '".$device."'");

//R�ckgabe
echo "SET ".$device." IN ".$room." TO ".$zustand;

?>