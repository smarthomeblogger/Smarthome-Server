<?php

//Der Parameter $type wurde hinzugefügt
function getModes($room, $type, $device, $db){
	
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
		//Ein switch-Block für die einzelnen Geräte-Typen wurde erstellt
		switch($type){
			case "Funksteckdose":
				//Die Zeilen 33-39 wurden dem Fall für den Gerätetypen "Funksteckdose" zugeordnet
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