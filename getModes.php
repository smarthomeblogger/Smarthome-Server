<?php

function getModes($room, $type, $device, $db){
	
	//Diese Zeilen wurden hinzugefügt
	if(!hasPermission($room, $db)){
		return "nopermission";
	}
	
	if($device==""){
		//Funksteckdosen laden
		$results = $db->prepare("SELECT * FROM 'funksteckdosen' WHERE ROOM == :room");
		$results->execute(array('room' => $room));
		
		$modi = array();
		
		foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
			$mode_item = array('device' => $row['DEVICE'], 'mode' => $row['ZUSTAND'],
			'icon' => $row['ICON'], 'name' => $row['NAME']);
			array_push($modi, $mode_item);
		}
		
		//Weitere Gerätetypen laden
		
		header('Content-type: application/json');
		return json_encode(array('modi' => $modi));
	}
	else{
		switch($type){
			case "Funksteckdose":
				//Schaltzustand aus Datenbank laden
				$results = $db->prepare("SELECT * FROM 'funksteckdosen' WHERE DEVICE == :device");
				$results->execute(array('device' => $device));
				
				foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
					return $row['ZUSTAND'];
				}
				break;
				
				//Weitere Cases
				
			default:
				return "nosuchtype";
		}
	}
}

?>