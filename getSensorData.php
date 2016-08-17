<?php

function getSensorData($room, $val, $show_einheit, $db)
{
	$ipAdress = $_SERVER['SERVER_ADDR'];	//IP-Adresse des Raspberry herausfinden
	
	if($room=="all")	//Alle Werte werden abgefragt
	{
		//Räume laden
		$results = $db->prepare("SELECT * FROM 'ROOMS'");
		$results->execute();

		$values = array();	//Ausgabearray erzeugen
		
		//Alle Räume durchlaufen
		foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
			
			$value_array = array();	//Wertearray erzeugen
			
			//Sensoren im aktullen Raum laden
			$ergebnisse = $db->prepare("SELECT * FROM 'ZWAVE_SENSOREN' WHERE RAUM=='".$row['LOCATION']."'");
			$ergebnisse->execute();
			
			//Alle Sensoren im aktuellen Raum durchlaufen
			foreach($ergebnisse->fetchAll(PDO::FETCH_ASSOC) as $reihe){
				//Wert für jeden Sensor zusammen mit Sensorart in Wertearray schreiben
				$value = array('shortform' => $reihe['SHORTFORM'], 'sensorart' => $reihe['SENSORART'], 'wert' => getData($reihe['RAUM'], $reihe['SENSORART'], 1, $db));
				array_push($value_array, $value);
			}
			
			//Daten für aktuellen Raum in Ausgabearray schreiben
			$value_item = array('name' => $row['NAME'], 'location' => $row['LOCATION'], 'value_array' => $value_array);
			array_push($values, $value_item);
		}

		//JSON-Objekt zurückgeben
		return json_encode(array('values' => $values));
	}
	else	//Ein spezieller Raum wird abgefragt
	{
		$id = getZwaveId($room, $val, $db);	//ID des gesuchten Sensors in gesuchtem Raum abfragen
		
		if($id !== "N/A"){
			//Z-Wave API aufrufen
			$link = "http://".$ipAdress.":8083/ZAutomation/api/v1/devices/".$id;
			//$result = file_get_contents($link);
		
			$cURL = curl_init($link); //Initialise cURL with the URL to connect to
			curl_setopt($cURL, CURLOPT_PORT, 8083); //Set the port to connect to
			curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true); //Get cURL to return the HTML from the curl_exec function

			$result = curl_exec($cURL); //Execute the request and store the result in $HTML

			echo $result; //Output the HTML
		
			//Wert je nach Wunsch mit/ohne Einheit ausgeben
			$array = json_decode($result, true);
			
			if($show_einheit==="1")
			{
				return $array['data']['metrics']['level']." ".$array['data']['metrics']['scaleTitle'];
			}
			else return $array['data']['metrics']['level'];
		}
		else return "N/A";	//Sensor-ID nicht gefunden?
	}
}

function getZwaveID($room, $val, $db){
	//Z-WAVE ID des gesuchten Sensors im gesuchten Raum laden
	$query = $db->query("SELECT * FROM 'ZWAVE_SENSOREN' WHERE RAUM == '".$room."' AND SENSORART == '".$val."'");

	//ID zurückgeben, wenn gefunden
	if($result = $query->fetch(PDO::FETCH_ASSOC)){
		return $result['ID'];
	}
	else return "N/A"; //ID nicht gefunden?
}

?>