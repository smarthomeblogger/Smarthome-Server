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

//Noch nicht im Tutorial
include "getWeather.php";
include "tvProgramm.php";
include "morningInfo.php";
include "voicecommand/voiceCommand.php";
include "getDIYDevices.php";
include "diyDeviceControl.php";
include "rfidControls.php";
include "manager.php";

include "getReedData.php";
include "heatingControl.php";

//Heizpläne
include "heatingscheme/heatingSchemeManager.php";

//Serverkey zum ansteuern der IoT-Geräte
function getServerKey(){
	return "SERVER";
}

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
            echo createScene($_POST['devices'], $_POST['rooms'], $_POST['types'], $_POST['values'],
				$_POST['conditions'], $_POST['room'], $_POST['name'], $db);
            break;
        case "getscenes":
            echo getScenes($_POST['room'], $db);
            break;
        case "getsysteminfo":
            echo getSystemInfo();
            break;
			
		//Noch nicht im Tutorial
		case "settvchannels":
			echo setTVChannels($_POST['device'], $_POST['room'], 'getinfo', "", $db);
			break;
		case "settvchannels":
			echo getAllTVChannels();
			break;
		case "shouldshowmorninginfo":
			echo iotDeviceControl($_POST['device'], $_POST['room'], 'getinfo', "", $db);
			break;
		case "getmorninginfo":
			echo iotDeviceControl($_POST['device'], $_POST['room'], 'getinfo', "", $db);
			break;
		case "getmorninginfosettings":
			echo iotDeviceControl($_POST['device'], $_POST['room'], 'getinfo', "", $db);
			break;
		case "setmorninginfosettings":
			echo iotDeviceControl($_POST['device'], $_POST['room'], 'getinfo', "", $db);
			break;
		case "rfidvalidation":
			echo rfidValidation($_POST['uid'], $db);
			break;
		case "rfidtrigger":
			echo rfidTrigger($_POST['uid'], $_POST['triggerid'], $db);
			break;
		case "heatingcontrol":
			echo heatingControl($_POST['room'], $_POST['type'], $_POST['id'], $_POST['value'], $db);
			break;
		case "getheatingdata":
			echo getHeatingData($_POST['room'], $_POST['type'], $_POST['id'], $_POST['all_data'], $db);
			break;
		case "voicecommand":
			echo voiceCommand($_POST['username'], $_POST['text'], $db);
			break;
		case "controldiydevice":
			echo diyDeviceControl($_POST['device'], $_POST['room'], $_POST['iotaction'], $_POST['value'], $db);
			break;
		case "getdiyinfo":
			echo diyDeviceControl($_POST['device'], $_POST['room'], 'getinfo', "", $db);
			break;
			
		case "getreeddata":
			echo getReedData($_POST['room'], $_POST['type'], $_POST['id'], $db);
			break;
			
		//Heizpläne
		case "addeditheatingschemeitem":
			echo addEditHeatingSchemeItem($_POST['id'], $_POST['time'], $_POST['value'],
				$_POST['active'], $_POST['days'], $_POST['data'], $db);
			break;
		case "deleteheatingschemeitem":
			echo deleteHeatingSchemeItem($_POST['id'], $db);
			break;
		case "getheatingschemeitems":
			echo getHeatingSchemeItems($_POST['day'], $_POST['rooms'],  $db);
			break;
		
		//Manager
		case "addedituser":
			echo addEditUser($_POST['name'], $_POST['psw'], $_POST['ip'], $_POST['permissions'], $db);
			break;
		case "getusers":
			echo getUsers($db);
			break;
		case "deleteuser":
			echo deleteUser($_POST['name'], $db);
			break;
		case "addeditroom":
			echo addEditRoom($_POST['roomname'], $_POST['location'], $_POST['icon'], $db);
			break;
		case "deleteroom":
			echo deleteRoom($_POST['location'], $db);
			break;
		case "moveitemsanddeleteoldroom":
			echo moveItemsAndDeleteOldRoom($_POST['oldroom'], $_POST['newroom'], $db);
			break;
		case "deleteroomwithitems":
			echo deleteRoomWithItems($_POST['location'], $db);
			break;
		case "getzwavedevices":
			echo getZWaveDevices($db);
			break;
		case "getdevicedata":
			echo getDeviceData($_POST['type'], $_POST['id'], $db);
			break;
		case "deletedevice":
			echo deleteDevice($_POST['type'], $_POST['id'], $db);
			break;
		case "addeditdevice":
			echo addEditDevice($_POST['id'], $_POST['type'], $_POST['data'], $db);
			break;
		case "getgateways":
			echo getGateways($db);
			break;
		case "getgatewaydata":
			echo getGatewayData($_POST['type'], $db);
			break;
		case "addeditgateway":
			echo addEditGateway($_POST['type'], $_POST['ip'], $_POST['port'], $_POST['usr'], $_POST['psw'], $_POST['changepw'], $db);
			break;
		case "getgatewaytypes":
			echo getGatewayTypes($db);
			break;
    }
}

//echo getDeviceData("Funksteckdose", "1", $db);
//echo getZWaveDevices($db);
//echo addEditRoom("Küche", "", "room", $db);
//echo getRoomData("sleeproom", $db);
//echo getGatewayData("Z-Wave", $db);
//echo getGatewayTypes($db);
//echo addEditGateway("Z-Wave", "localhost", "8083", "admin", "4a456a59", "true", $db);
//echo getReedData("sleeproom", "DIY Fensterkontakt", "1", $db);

//echo getHeatingSchemeItems("2", "", $db);

//echo addEditHeatingSchemeItem("", "", "", "", "", "", $db);

function validateUser($username, $password, $db){
    //wird noch implementiert
      
    return true;
}

function hasPermission($action, $db){
	//$permissions = getPermissions($_POST['username'], $db);
	$permissions = getPermissions("sascha", $db);
	$permissions = json_decode($permissions, true)['permissions'];
	
	return (in_array($action, $permissions) || in_array("admin", $permissions));
}

?>