<?php
//Datenbankverbindung herstellen
$SQLITEdb = "database/data.sqlite";
$db = new PDO("sqlite:".$SQLITEdb);

//Funksteckdosen
include "getModes.php";
include "setModes.php";

include "getRooms.php";
include "getSensorData.php";

//Szenen
include "scenes/getScenes.php";
include "scenes/runScene.php";
include "scenes/createScene.php";

$validUser = validateUser($_GET['username'], $_GET['password'], $db);

if($validUser){
	switch($_GET['action']){
		case "getrooms":
		case "login":
			echo getRooms($db);
			break;
		case "getmodes":
			echo getModes($_GET['room'], $_GET['device'], $db);
			break;
		case "setmodes":
			echo setModes($_GET['room'], $_GET['device'], $_GET['mode'], $db);
			break;
		case "getsensordata":
			echo getSensorData($_GET['room'], $_GET['value'], $_GET['showeinheit'], $db);
			break;
		case "runscene":
			echo runScene($_GET['room'], $_GET['name'], $db);
			break;
		case "createscene":
			echo createScene($_GET['devices'], $_GET['rooms'], $_GET['types'], $_GET['values'], $_GET['conditions'], $_GET['room'], $_GET['name'], $db);
			break;
		case "getscenes":
			echo getScenes($_GET['room'], $db);
			break;
		case "voicecommand":
			echo "voicecontrol";
			break;
		case "getheiztemp":
			echo "20";
			break;
		case "getweather":
			echo "wetter";
			break;
		default:
			if($_GET['action']=="getsensordata"){
				echo getSensorData($_GET['room'], $_GET['value'], $_GET['showeinheit'], $db);
			}
			break;
	}
}

function validateUser($username, $password, $db){
	//falsche daten: exit("wrongdata");
	
	//unbekannter nutzer: ecit("unknownuser");
	
	return true;
}

?>