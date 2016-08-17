<?php

function getRooms($db){
	//R�ume laden
	$results = $db->prepare("SELECT * FROM 'ROOMS'");

	$results->execute();

	$rooms = array();

	//JSON-Objekt erstellen und f�llen
	foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
		$room_item = array('name' => $row['NAME'], 'location' => $row['LOCATION']);
		array_push($rooms, $room_item);
	}

	//JSON-Objekt ausgeben
	header('Content-type: application/json');
	return json_encode(array('rooms' => $rooms));
}

?>