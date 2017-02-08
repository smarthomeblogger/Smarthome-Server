<?php
 
function getRoomData($room, $db){
	
	//Diese Zeilen wurden hinzugefügt
	if(!hasPermission($room, $db)){
		return "nopermission";
	}
	
    $roomdata = array();
    
	//Funksteckdosen abfragen
	$types = array("Funksteckdose");
	
	foreach($types as $type){
		$switchdata = json_decode(getModes($room, $type, "", $db), true);
		foreach($switchdata['modi'] as $switch){
			if($switch['mode'] === "1"){
				$mode = true;
			}
			else{
				$mode = false;
			}
			array_push($roomdata, array('name' => $switch['name'], 'device' => $switch['device'],'device_type' => $type,
			'icon' => $switch['icon'], 'type' => "switch", 'value' => ($mode) ? 'true' : 'false'));
		}
	}
	
    $sensordata = json_decode(getSensorData($room, "", "", "", $db), true);
    foreach($sensordata['values'][0]['value_array'] as $sensor){
        array_push($roomdata, array('name' => $sensor['shortform'], 'device' => $sensor['id'],
		'icon' => $sensor['icon'], 'type' => "value", 'device_type' => $sensor['device_type'], 'value' => $sensor['wert']));
    }
     
    $scenedata = json_decode(getScenes($room, $db), true);
    if(sizeOf($scenedata['scenes']) > 0){
        array_push($roomdata, array('name' => "Szenen", 'device' => "scenes", 'icon' => "scenes", 'type' => "scenes", 'value' => ""));
    }
     
	 
	//Neu dazugekommen
    $iotdata = json_decode(getIotDevices($room, $db), true);
    foreach($iotdata['iotdevices'] as $device){
        array_push($roomdata, array('name' => $device['name'], 'device' => $device['device'], 'icon' => $device['icon'],
		'type' => 'iotdevice', 'value' => "", 'device_type' => "IoT Gerät"));
    }
     
    //Heizungs-Item implementieren
	$thermostate = json_decode(getHeatingData($room, "", "", "1", $db), true);
    foreach($thermostate['thermostate'][0]['thermostat_array'] as $thermostat){
        array_push($roomdata, array('name' => $thermostat['name'], 'device' => $thermostat['id'],
		'icon' => $thermostat['icon'], 'type' => "heating", 'device_type' => $thermostat['device_type'], 'value' => $thermostat['data']['value'],
		'einheit' => $thermostat['data']['einheit'], 'min' => $thermostat['data']['min'], 'max' => $thermostat['data']['max'], 'step' => 0.5));
    }
     
    return json_encode(array('roomdata' => $roomdata));
}
 
?>