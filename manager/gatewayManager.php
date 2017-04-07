<?php

//Steuerungs-Zentralen
function addEditGateway($key, $ip, $port, $usr, $psw, $changepw, $db){
	if(!hasPermission("admin", $db)){
		return "noadmin";
	}
	
	$paramArray = array("name" => $key, "ip" => $ip, "port" => $port, "usr" => $usr);
	
	if($changepw === "true"){
		$paramArray['psw'] = $psw;
		
		//Gateway bearbeiten bzw. hinzufügen
		$updateQuery = $db->prepare("UPDATE OR IGNORE 'GATEWAYS' SET IP = :ip, PORT = :port, USERNAME = :usr, PASSWORD = :psw WHERE NAME = :name;");
	
		$insertQuery = $db->prepare("INSERT OR IGNORE INTO 'GATEWAYS' (NAME, IP, PORT, USERNAME, PASSWORD) VALUES (:name, :ip, :port, :usr, :psw);");
	}
	else{
		//Gateway bearbeiten bzw. hinzufügen
		$updateQuery = $db->prepare("UPDATE OR IGNORE 'GATEWAYS' SET IP = :ip, PORT = :port, USERNAME = :usr WHERE NAME = :name;");
	
		$insertQuery = $db->prepare("INSERT OR IGNORE INTO 'GATEWAYS' (NAME, IP, PORT, USERNAME) VALUES (:name, :ip, :port, :usr);");
	}
	
	$updateQuery->execute($paramArray);
	$insertQuery->execute($paramArray);
	
	if($updateQuery == true && $insertQuery == true) return "ok";
	else return "error";
}

function getGateway($key, $db){
	$results = $db->prepare("SELECT * FROM 'GATEWAYS' WHERE NAME == :key");
	$results->execute(array('key' => $key));
	
	//Alle Server durchlaufen
	foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
		return $row;
	}
}

function getGatewayData($key, $db){
	if(!hasPermission("admin", $db)){
		return "noadmin";
	}
	
	$results = $db->prepare("SELECT * FROM 'GATEWAYS' WHERE NAME == :key");
	$results->execute(array('key' => $key));
	
	//Alle Server durchlaufen
	foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
		return json_encode(array("gatewaydata" => array("name" => $row['NAME'], "ip" => $row['IP'], "port" => $row['PORT'],
		"username" => $row['USERNAME'])));
	}
}

function getGateways($db){
	if(!hasPermission("admin", $db)){
		return "noadmin";
	}
	
	$results = $db->prepare("SELECT * FROM 'GATEWAYS'");
	$results->execute();
	
	$gateways = array();
	
	//Alle Server durchlaufen
	foreach($results->fetchAll(PDO::FETCH_ASSOC) as $row){
		array_push($gateways, array("name" => $row['NAME'], "ip" => $row['IP'], "port" => $row['PORT'], "username" => $row['USERNAME']));
	}
	
	return json_encode(array("gateways" => $gateways, "gatewaytypes" => getGatewayTypes($db)));
}

function getGatewayTypes($db){
	if(!hasPermission("admin", $db)){
		return "noadmin";
	}
	
	$gatewayTypes = array("Z-Wave", "MAX! Cube");
	
	$query = $db->prepare("SELECT NAME FROM GATEWAYS");
	$query->execute();
	
	foreach($query->fetchAll() as $row){
		$index = array_search($row['NAME'], $gatewayTypes);
		if($index !== FALSE){
			unset($gatewayTypes[$index]);
		}
	}
	
	return array_values($gatewayTypes);
}

?>