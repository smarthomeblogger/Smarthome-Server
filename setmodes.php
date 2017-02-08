<?php
  
function setModes($room, $type, $device, $zustand, $db){
	
	//Diese Zeilen wurden hinzugefügt
	if(!hasPermission($room, $db)){
		return "nopermission";
	}
	
	switch($type){
		case "Funksteckdose":
			//Hauscode und Steckdosennummer aus Datenbank laden
			$query = $db->prepare("SELECT * FROM 'funksteckdosen' WHERE DEVICE == :device");
			$query->execute(array('device' => $device));
			 
			//Abfrageergebnisse speichern
			if($result = $query->fetch(PDO::FETCH_ASSOC)){
				$hauscode = $result['HAUSCODE'];
				$steckdosennummer = $result['STECKDOSENNUMMER'];
			}
			 
			//Schaltbefehl für Steckdosen
			shell_exec("/usr/local/bin/send  ".$hauscode." ".$steckdosennummer." ".$zustand);
		  
			//Status der geschalteten Steckdose aktualisieren
			$query = $db->prepare("UPDATE 'funksteckdosen' SET 'ZUSTAND' = :zustand WHERE DEVICE == :device");
			$query->execute(array('zustand' => $zustand, 'device' => $device));
			break;
		default:
			return "nosuchtype";
			break;
	}
  
    //Rückgabe
    return "ok";
}
  
?>