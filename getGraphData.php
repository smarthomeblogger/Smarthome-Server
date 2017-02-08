<?php
 
function getGraphData($type, $id, $von, $bis, $db){
     
    //Diese Zeilen wurden hinzugefügt
    if(!hasPermission($type, $db)){
        return "nopermission";
    }
     
    if($von == $bis){
        //Datum formattieren
        $datum = date("Y-m-d", strtotime($von));
         
        return getDayData($type, $id, $datum, $db);
    }
    else{
        return getDayMinMax($type, $id, $von, $bis, $db);
    }
}
 
function getDayMinMax($type, $id, $start, $ende, $db){   
    $start = date("Y-m-d", strtotime($start));
	
	$ende = date("Y-m-d", strtotime($ende));
     
    $values = array();
     
    $query = $db->query("SELECT MIN(VALUE) as MIN, MAX(VALUE) as MAX, strftime(\"%d.%m.%Y\", DATETIME) as DATE FROM SENSOR_DATA WHERE DEVICE_TYPE == :type
	AND DEVICE_ID == :id AND DATETIME >= :start AND DATETIME <= :ende GROUP BY DATE ORDER BY DATETIME ASC");
	$query->execute(array('type' => $type, 'id' => $id, 'start' => "$start 00:00", 'ende' => "$ende 23:59"));
	
	foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row){
		array_push($values, array('date' => $row['DATE'], 'min' => $row['MIN'], 'max' => $row['MAX']));
	}
	
    return json_encode(array('values' => $values, 'einheit' => getEinheit($type, $id, $db)));
}

function getEinheit($type, $id, $db){
	switch($type){
		case "Z-Wave Sensor":
			$query = $db->query("SELECT * FROM 'ZWAVE_SENSOREN' WHERE ID == :id");
			$query->execute(array('id' => $id));
			break;
	}
     
    if($result = $query->fetch(PDO::FETCH_ASSOC)){
        return $result['EINHEIT'];
    }
	else{
		return "";
	}
}

function getDayData($type, $id, $datum, $db){    
    $values = array();
     
    $query = $db->query("SELECT * FROM 'SENSOR_DATA' WHERE DEVICE_TYPE == :type AND DEVICE_ID == :id AND DATETIME >= :start AND DATETIME < :ende ORDER BY DATETIME ASC");
    $query->execute(array('type' => $type, 'id' => $id, 'start' => $datum." 00:00", 'ende'=> $datum." 23:59"));
     
    foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row){
        $value_item = array('value' => floatval($row['VALUE']), 'time' => str_replace($datum." ", "", $row['DATETIME']));
        array_push($values, $value_item);
    }
    
	switch($type){
		case "Z-Wave Sensor":
			$query = $db->query("SELECT * FROM 'ZWAVE_SENSOREN' WHERE ID == :id");
			$query->execute(array('id' => $id));
			break;
	}
     
    if($result = $query->fetch(PDO::FETCH_ASSOC)){
        $einheit = $result['EINHEIT'];
    }
     
    return json_encode(array('values' => $values, 'einheit' => getEinheit($type, $id, $db)));
}
 
?>