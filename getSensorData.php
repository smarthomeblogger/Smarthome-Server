<?php

function getSensorData($room, $type, $id, $show_einheit, $db){
	$server_ip = $_SERVER['SERVER_ADDR'];
	
	if($room === "") return "error";
	
	if(!hasPermission($room, $db)){
		return "nopermission";
	}
	
	if($type != "" && $id != ""){
		switch($type){
			case "Z-Wave Sensor":
				if($id !== null){
					
					//Geändert
					$data = getControlServer("Z-Wave", $db);
		
					$link = "http://".$data['IP'].":".$data['PORT']."/ZAutomation/api/v1/devices/".$id;
					
					//Abfrage ausführen
					$result = exec("wget -q -O - --auth-no-challenge --user=".$data['USERNAME']." --password=".$data['PASSWORD']." ".$link);
					
					//return $result;
					
					//Wert je nach Wunsch mit/ohne Einheit ausgeben
					$array = json_decode($result, true);
					
					//Response-Code prüfen (200 = ok)
					if($array['code'] != "200"){
						return "N/A";
					}
					//Geändert ende
					
					$return = $array['data']['metrics']['level'];
					
					if($show_einheit == "1"){
						$return = $return." ".$array['data']['metrics']['scaleTitle'];
					}
					
					return $return;
				}
				else return "N/A";
				
			case "IoT Sensor":
				$results = $db->prepare("SELECT * FROM 'IOT_SENSORS' WHERE ID == :id");
				$results->execute(array('id' => $id));
				
				foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
					if($row['CHANNEL'] == null || $row['CHANNEL'] == ""){
						$link = $row['IP']."/key=".getServerKey()."&";
					}
					else{
						$link = $row['IP']."/key=".getServerKey();
					}
					
					$result = exec("wget -q -O - $link");
					
					if($result == null || $result == ""){
						return "N/A";
					}
					else{
						$result = json_decode($result, true)['value'];
					}
					
					if($show_einheit == "1"){
						$result = $result." ".$row['EINHEIT'];
					}
					
					return $result;
				}
				
			//Case für weitere Sensorarten
			//...
		}
	}
	else{
		//Räume laden
		if($room == "all"){
			$results = $db->prepare("SELECT * FROM 'ROOMS'");
			$results->execute();
		}
		else{
			$results = $db->prepare("SELECT * FROM 'ROOMS' WHERE LOCATION == :room");
			$results->execute(array('room' => $room));
		}
		
		//ausgabearray erzeugen
		$values = array();
		
		//Alle Räume durchlaufen
		foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
			if(!hasPermission($room, $db)){
				return "nopermission";
			}
				
			//Wertearray erzeugen
			$value_array = array();
			
			//Alle Z-Wave Sensoren im aktuellen Raum laden
			$type = "Z-Wave Sensor";
			$ergebnisse = $db->prepare("SELECT * FROM 'ZWAVE_SENSOREN' WHERE RAUM == :location");
			$ergebnisse->execute(array('location' => $row['LOCATION']));
			
			//Alle Z-Wave Sensoren im Raum durchlaufen
			foreach($ergebnisse->fetchAll(PDO::FETCH_ASSOC) as $reihe){
				//Wert für jeden Sensor zusammen mit Sensorart in Wertearray schreiben
				$value = array('shortform'=> $reihe['SHORTFORM'], 'id' => $reihe['ID'], 'device_type' => $type, "icon" => $reihe['ICON'],
				'wert' => getSensorData($row['LOCATION'], $type, $reihe['ID'], 1, $db));
				array_push($value_array, $value);
			}
			
			//Alle IoT Sensoren im aktuellen Raum laden
			$type = "IoT Sensor";
			$ergebnisse = $db->prepare("SELECT * FROM 'IOT_SENSORS' WHERE RAUM == :location");
			$ergebnisse->execute(array('location' => $row['LOCATION']));
			
			//Alle IoT Sensoren im Raum durchlaufen
			foreach($ergebnisse->fetchAll(PDO::FETCH_ASSOC) as $reihe){
				//Wert für jeden Sensor zusammen mit Sensorart in Wertearray schreiben
				$value = array('shortform'=> $reihe['NAME'], 'id' => $reihe['ID'], 'device_type' => $type, "icon" => $reihe['ICON'],
				'wert' => getSensorData($row['LOCATION'], $type, $reihe['ID'], 1, $db));
				array_push($value_array, $value);
			}
			
			//Abfrage für weitere Sensorarten
			//...
			
			//Daten für aktuellen Raum in Ausgabearray schreiben
			$value_item = array('name' => $row['NAME'], 'location' => $row['LOCATION'], 'value_array' => $value_array);
			array_push($values, $value_item);
		}
		
		//JSON-Objekt zurückgeben
		return json_encode(array('values' => $values));
	}
}

?>