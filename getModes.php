<?php

function getModes($room, $device, $db){
	if($device==''){
		//Daten des gesuchten Raumes aus der Datenbnak laden
		$results = $db->prepare("SELECT * FROM 'funksteckdosen' WHERE ROOM == :room");
		$results->execute(array('room' => $room));
	
		$modi = array();
	
		foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
			$mode_item = array('device' => $row['DEVICE'], 'mode' => $row['ZUSTAND'], 'icon' => $row['ICON'], 'name' => $row['NAME']);
			array_push($modi, $mode_item);
		}
	
		header('Content-type: application/json');
		return json_encode(array('modi' => $modi));
	}
	else{
		//Schaltzustand aus Datenbank laden
		$results = $db->prepare("SELECT * FROM 'funksteckdosen' WHERE ROOM == :room AND DEVICE == :device");
		$results->execute(array('room' => $room, 'device' => $device));
	
		$modi = array();
	
		foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
			return $row['ZUSTAND'];
		}
	}
}
 
?>