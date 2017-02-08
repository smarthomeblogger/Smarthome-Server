<?php

function getScenes($room, $db){

	//Szenen aus Datenbank laden
	//INFO: Es werden alle Szenen abgefragt, die entweder dem Raum des Parameters $room oder dem Raum 'NONE' zugewiesen sind
	$results = $db->prepare("SELECT * FROM 'SCENES' WHERE ROOM == :room OR ROOM == 'NONE'");
	$results->execute(array('room' => $room));
	
	$scenes = array();
	
	//Namen aller gefundenen Szenen in Array schreiben
	foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
		
		//Diese Zeilen wurden hinzugefügt
		if(!hasPermission($row['ROOM'], $db) && $row['ROOM'] !== "NONE"){
			continue;
		}
		
		$scene_item = array('name' => $row['NAME'], 'room' => $row['ROOM'], 'actions' => $row['ACTIONS']);
		array_push($scenes, $scene_item);
	}
	
	//Array als JSON-Objekt ausgeben
	header('Content-type: application/json');
	return json_encode(array('scenes' => $scenes));
}

?>