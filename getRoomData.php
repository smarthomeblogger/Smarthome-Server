<?php
 
function getRoomData($room, $db){
	
	if(!hasPermission($room, $db)){
		return "nopermission";
	}
	
    $roomdata = array();
    
	//Diese Zeile wrde hinzugefügt
	$types = array("Funksteckdose");
	
	//Die Zeilen 16-28 wurden in eine foreach-Schleife gepackt
	foreach($types as $type){
		//Die Parameter wurden angepasst
		$switchdata = json_decode(getModes($room, $type, "", $db), true);
		foreach($switchdata['modi'] as $switch){
			if($switch['mode'] === "1"){
				$mode = true;
			}
			else{
				$mode = false;
			}
			//Die Werte des Arrays wurden angepasst
			array_push($roomdata, array('name' => $switch['name'], 'device' => $switch['device'], 'device_type' => $type,
			'icon' => $switch['icon'], 'type' => "switch", 'value' => ($mode) ? 'true' : 'false'));
		}
	}
	
	//Die Parameter wurden angepasst
    $sensordata = json_decode(getSensorData($room, "", "", "", $db), true);
    foreach($sensordata['values'][0]['value_array'] as $sensor){
		//Die Werte des Arrays wurden angepasst
        array_push($roomdata, array('name' => $sensor['shortform'], 'device' => $sensor['id'],
		'icon' => $sensor['icon'], 'type' => "value", 'device_type' => $sensor['device_type'], 'value' => $sensor['wert']));
    }
     
    $scenedata = json_decode(getScenes($room, $db), true);
    if(sizeOf($scenedata['scenes']) > 0){
        array_push($roomdata, array('name' => "Szenen", 'device' => "scenes", 'icon' => "scenes", 'type' => "scenes", 'value' => ""));
    }
     
    //Heizungs-Item implementieren
     
    return json_encode(array('roomdata' => $roomdata));
}
 
?>