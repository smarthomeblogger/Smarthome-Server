<?php

function runScene($room, $scene, $db){
	
	//Diese Zeilen wurden hinzugef�gt
	if(!hasPermission($room, $db) && $room !== "NONE"){
		continue;
	}
	
	//Szenen aus Datenbank laden
	$results = $db->prepare("SELECT * FROM 'SCENES' WHERE ROOM == :room AND NAME == :scene");
	$results->execute(array('room' => $room, 'scene' => $scene));
	
	foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
		$array = json_decode($row['ACTIONS'], true);
		foreach($array['actions'] as $action){
			//Wenn Bedingung erf�llt ist oder keine Bedingung angegeben, f�hre Befehl aus
			if($action['if']==null || conditionTrue($action['if'])){
				//Verarbeitung der Aktion anhand des Aktionstyps (Schalter, Heizung, etc.)
				switch($action['type']){
					case 'switch':
						//Hier wurden Parameter angepasst
						setModes($action['location'], $action['type'],  $action['device'], $action['value'], $db);
						break;
					case 'heizung':
						//Heizungssteuerung wird noch implementiert
						break;
				}
			}
		}
	}
	
	return "ok";
}

//Pr�ft die �bergebene Bedingung auf Wahrheit und gibt dementsprechend true/false zur�ck
function conditionTrue($condition){
	switch($condition['type']){
		//Sensorwert abfragen
		case 'sensor':
			$value = getData($condition['room'], $condition['sensorart'], '', $db);
			break;
		//Schalter abfragen
		case 'switch':
			//Hier wurden Parameter angepasst
			$value = getModes($condition['room'], $condition['type'], $condition['device'], $db);
			break;
	}
	
	//vergleicht den abgefragten Wert mit dem �bergebenen Wert
	switch($condition['comparator']){
		case '<':
			return ($value < $condition['value']);
		case '<=':
		case '=<':
			return ($value <= $condition['value']);
		case '>':
			return ($value > $condition['value']);
		case '>=':
		case '=>':
			return ($value >= $condition['value']);
		case '==':
		case '=':
			return ($value == $condition['value']);
		default:
			return false;
	}
}

?>