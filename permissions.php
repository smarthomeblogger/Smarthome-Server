<?php

function getPermissions($username, $db){
	//Berechtigungen des Nutzers aus der Datenbank laden
	$results = $db->prepare("SELECT * FROM 'userdata' WHERE USERNAME == :username");
	$results->execute(array('username' => $username));
	
	//Abfrageergebnisse speichern
	if($result = $results->fetch(PDO::FETCH_ASSOC)){
		$permissions = $result['PERMISSIONS'];
	}
	
	return $permissions;
}

function setPermissions($username, $permissions, $db){
	//Berechtigungen des Nutzers aktualisieren
	$results = $db->prepare("UPDATE 'userdata' SET PERMISSIONS = :permissions WHERE USERNAME == :username");
	$results->execute(array('username' => $username, 'permissions' => $permissions));
	
	return "ok";
}

?>