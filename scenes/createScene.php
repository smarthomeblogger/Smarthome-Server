<?php

function createScene($devices, $rooms, $types, $values, $conditions, $room, $name, $db){
	//Prüfen, ob  alle Arrays ($devices, $rooms, $types, $modes) gleich viele Elemente haben, gibt andernfalls Fehlermeldung ausgeben
	if(sizeOf($devices) < sizeOf($rooms) ||
		sizeOf($rooms) < sizeOf($types) ||
		sizeOf($types) < sizeOf($values) ||
		sizeOf($values) < sizeOf($devices)){
		exit("error");
	}
	
	$scene = array();
	
	//Die Übergabe-Arrays in ein einziges Array zusammensetzen
	for($counter = 0; $counter < sizeOf($devices); $counter++){
		$action_item = array('device' => $devices[$counter], 'location' => $rooms[$counter], 'value' => $values[$counter], 'type' => $types[$counter], 'if' => $conditions[$counter]);
		array_push($scene, $action_item);
	}
	
	//Array als JSON-Objekt ausgeben
	header('Content-type: application/json');
	$action_string = json_encode(array('actions' => $scene));
	
	//Prüfen, ob $room leer ist, wenn ja Default-Wert setzen
	if($room == ''){
		$room = 'none';
	}
	
	//Szene in Datenbank schreiben
	$statement = $db->prepare("INSERT INTO SCENES(NAME, ROOM, ACTIONS)VALUES(?,?,?)");
	$statement->execute(array($name ,$room, $action_string));
	
	return "ok";
}

?>