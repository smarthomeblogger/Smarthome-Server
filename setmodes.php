<?php

//Der Parameter $type wurde hinzugef�gt
function setModes($room, $type, $device, $zustand, $db){
	
	if(!hasPermission($room, $db)){
		return "nopermission";
	}
	
	//Ein switch-Block f�r verschiedene Ger�te-Typen wurde erstellt
	switch($type){
		case "Funksteckdose":
			//Die Zeilen 14-29 wurden dem Fall "Funksteckdose" zugeordnet
			//Hauscode und Steckdosennummer aus Datenbank laden
			$query = $db->prepare("SELECT * FROM 'funksteckdosen' WHERE DEVICE == :device");
			$query->execute(array('device' => $device));
			 
			//Abfrageergebnisse speichern
			if($result = $query->fetch(PDO::FETCH_ASSOC)){
				$hauscode = $result['HAUSCODE'];
				$steckdosennummer = $result['STECKDOSENNUMMER'];
			}
			 
			//Schaltbefehl f�r Steckdosen
			shell_exec("/usr/local/bin/send  ".$hauscode." ".$steckdosennummer." ".$zustand);
		  
			//Status der geschalteten Steckdose aktualisieren
			$query = $db->prepare("UPDATE 'funksteckdosen' SET 'ZUSTAND' = :zustand WHERE DEVICE == :device");
			$query->execute(array('zustand' => $zustand, 'device' => $device));
			break;
		default:
			return "nosuchtype";
			break;
	}
  
    //R�ckgabe
    return "ok";
}
  
?>