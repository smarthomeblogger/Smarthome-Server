<?php

if($_GET['debug'] == "1"){
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
}

//Datenbankverbindung herstellen
$SQLITEdb = "database/data.sqlite";
$db = new PDO("sqlite:".$SQLITEdb);

//Funksteckdosen
include "getModes.php";
include "setModes.php";
  
//Andere
include "getRooms.php";
include "getSensorData.php";
include "getSystemInfo.php";
include "getRoomData.php";
include "getGraphData.php";
include "events.php";
include "permissions.php";

//Szenen
include "scenes/getScenes.php";
include "scenes/runScene.php";
include "scenes/createScene.php";

$validUser = validateUser($_POST['username'], $_POST['password'], $db);

if($validUser){
    switch($_POST['action']){
        case "getrooms":
            echo getRooms($db);
            break;
		case "getevents":
			echo getEvents($_POST['username'], $_POST['type'], $_POST['limit'], $_POST['offset'], $db);
			break;
		case "geteventtypes":
			echo getEventTypes($db);
			break;
		case "addevent":
			echo addEvent($_POST['type'], $_POST['text'], $db);
			break;
		case "getunseenevents":
			echo getUnseenEvents($_POST['username'], $db);
			break;
		case "getpermissions":
			echo getPermissions($_POST['user'], $db);
			break;
		case "setpermissions":
			echo setPermissions($_POST['user'], $_POST['permissions'], $db);
			break;
        case "getgraphdata":
            echo getGraphData($_POST['type'], $_POST['id'], $_POST['von'], $_POST['bis'], $db);
            break;
        case "getroomdata":
            echo getRoomData($_POST['room'], $db);
            break;
        case "getmodes":
            echo getModes($_POST['room'], $_POST['type'], $_POST['device'], $db);
            break;
        case "setmodes":
            echo setModes($_POST['room'], $_POST['type'], $_POST['device'], $_POST['zustand'], $db);
            break;
        case "getsensordata":
            echo getSensorData($_POST['room'], $_POST['type'], $_POST['id'], $_POST['showeinheit'], $db);
            break;
        case "runscene":
            echo runScene($_POST['room'], $_POST['name'], $db);
            break;
        case "createscene":
            echo createScene($_POST['devices'], $_POST['rooms'], $_POST['types'], $_POST['values'], $_POST['conditions'], $_POST['room'], $_POST['name'], $db);
            break;
        case "getscenes":
            echo getScenes($_POST['room'], $db);
            break;
        case "getsysteminfo":
            echo getSystemInfo();
            break;
    }
}

function validateUser($username, $password, $db){
    //wird noch implementiert
      
    return true;
}

function hasPermission($action, $db){
	$permissions = getPermissions($_POST['username'], $db);
	$permissions = json_decode($permissions, true)['permissions'];
	
	return (in_array($action, $permissions) || in_array("admin", $permissions));
}

?>