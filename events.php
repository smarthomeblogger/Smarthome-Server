<?php

function getEvents($username, $type, $limit, $offset, $db){
    //Ereignisse laden
	if($type !== ""){
		$results = $db->prepare("SELECT * FROM 'EVENTS' WHERE TYPE == :type ORDER BY TIMESTAMP DESC LIMIT :limit OFFSET :offset");
		$results->execute(array('type' => $type, 'limit' => $limit, 'offset' => $offset));
	}
	else{
		$results = $db->prepare("SELECT * FROM 'EVENTS' ORDER BY TIMESTAMP DESC LIMIT :limit OFFSET :offset");
		$results->execute(array('limit' => $limit, 'offset' => $offset));
	}

    $events = array();
 
    //JSON-Objekt erstellen und füllen
    foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
        $event_item = array('text' => $row['TEXT'], 'time' => $row['TIMESTAMP'], 'type' => $row['TYPE']);
        array_push($events, $event_item);
    }
 
	//EVENTS_LAST_CHECKED für $username mit aktueller Zeit aktualisieren
	$query = $db->prepare("UPDATE 'userdata' SET 'EVENTS_LAST_CHECKED' = :time WHERE USERNAME == :username");
	$query->execute(array('time' => time(), 'username' => $username));
 
    //JSON-Objekt ausgeben
    return json_encode(array('events' => $events));
}

function getUnseenEvents($username, $db){
	//EVENTS_LAST_CHECKED für Nutzer $username abfragen
	$query = $db->prepare("SELECT EVENTS_LAST_CHECKED FROM 'userdata' WHERE USERNAME == :username");
	$query->execute(array('username' => $username));
	
	$lastChecked = $query->fetch(PDO::FETCH_ASSOC)['EVENTS_LAST_CHECKED'];
	
	if($lastChecked != false){
	
		//Ereignisse zählen
		$results = $db->prepare("SELECT COUNT(*) FROM 'EVENTS' WHERE TIMESTAMP > :time");
		
		$results->execute(array('time' => $lastChecked));
	
		
		foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
			return $row['COUNT(*)'];
		}
	}
	else{
		return "usernotfound";
	}
}

function getEventTypes($db){
    //Eventtypen laden
    $results = $db->prepare("SELECT DISTINCT TYPE FROM EVENTS ORDER BY TYPE");
 
    $results->execute();
 
    $types = array();
 
    //JSON-Objekt erstellen und füllen
    foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
        array_push($types, $row['TYPE']);
    }
 
    //JSON-Objekt ausgeben
    return json_encode(array('types' => $types));
}

function addEvent($type, $text, $db){
	//Ereignis in Datenbank schreiben
	$statement = $db->prepare("INSERT INTO EVENTS (TYPE, TEXT, TIMESTAMP) VALUES (?,?,?)");
	$statement->execute(array($type, $text, time()));
	
	return "eventadded";
}

?>