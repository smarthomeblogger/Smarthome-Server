<?php
 
function getRooms($db){
    //R�ume laden
    $results = $db->prepare("SELECT * FROM 'ROOMS'");
 
    $results->execute();
 
    $rooms = array();
 
    //JSON-Objekt erstellen und f�llen
    foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
		
		//Diese Zeilen wurden hinzugef�gt
		if(!hasPermission($row['LOCATION'], $db)){
			continue;
		}
		
        $room_item = array('name' => $row['NAME'], 'location' => $row['LOCATION'], 'icon' => $row['ICON']);
        array_push($rooms, $room_item);
    }
 
    //JSON-Objekt ausgeben
    header('Content-type: application/json');
    return json_encode(array('rooms' => $rooms));
}
 
?>