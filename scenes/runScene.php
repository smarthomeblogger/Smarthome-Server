<?php

function runScene($room, $scene, $db){
	//Szenen aus Datenbank laden
	$results = $db->prepare("SELECT * FROM 'SCENES' WHERE ROOM == :room AND NAME == :scene");
	$results->execute(array('room' => $room, 'scene' => $scene));
	
	foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
		$array = json_decode($row['ACTIONS'], true);
		foreach($array['actions'] as $action)
		{	
			//Wenn Bedingung erfüllt ist oder keine Bedingung angegeben, führe Befehl aus
			if($action['if']==null || conditionTrue($action['if'])){
				//Verarbeitung der Aktion anhand des Aktionstyps (Schalter, Heizung, etc.)
				switch($action['type']){
					case 'switch':
						setModes($action['location'], $action['device'], $action['value'], $db);
						break;
					case 'heizung':
						//Heizungssteuerung wird noch implementiert
						break;
				}
			}
		}
	}
	
	echo "ok";
	
	//Prüft die übergebene Bedingung auf Wahrheit und gibt dementsprechend true/false zurück
	function conditionTrue($condition){
		switch($condition['type']){
			//Sensorwert abfragen
			case 'sensor':
				$value = getData($condition['room'], $condition['sensorart'], '', $db);
				break;
			//Schalter abfragen
			case 'switch':
				$value = getModes($condition['room'], $condition['device'], $db);
				break;
		}
		
		
		//vergleicht den abgefragten Wert mit dem übergebenen Wert
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
}

?>