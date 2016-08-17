<?php
 
function setModes($room, $device, $zustand, $db){
		//Hauscode und Steckdosennummer aus Datenbank laden
	$query = $db->prepare("SELECT * FROM 'funksteckdosen' WHERE ROOM == :room AND DEVICE == :device");
	$query->execute(array('room' => $room, 'device' => $device));
	
	//Abfrageergebnisse speichern
	if($result = $query->fetch(PDO::FETCH_ASSOC)){
		$hauscode = $result['HAUSCODE'];
		$steckdosennummer = $result['STECKDOSENNUMMER'];
	}
	
	$script = false;
	
	if($room=="sleeproom" && $device=="mediacenter" && $zustand=="0")
	{
		$script = true;
		shell_exec('sudo python /var/www/html/python/sleeproommediacentershutdown.py');
	}
	
	if($script==false)
	{
		//Schaltbefehl für Steckdosen
		shell_exec("/usr/local/bin/send  ".$hauscode." ".$steckdosennummer." ".$zustand);
	}
 
	//Status der geschalteten Steckdose aktualisieren
	$query = $db->exec("UPDATE 'funksteckdosen' SET 'ZUSTAND' = '".$zustand."' WHERE ROOM == '".$room."' AND DEVICE == '".$device."'");
 
	//Rückgabe
	return "SET ".$device." IN ".$room." TO ".$zustand;
}
 
?>