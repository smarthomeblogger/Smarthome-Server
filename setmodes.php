<?php

$hauscode = $_GET['hauscode']; //Der Hauscode der Steckdose 
$steckdosennummer = $_GET['nummer']; //Die Steckdosennummer [Zahl von 1-5]
$zustand = $_GET['zustand']; //Gew�nschter Zustand [1 f�r an; 0 f�r aus]

//Schaltbefehl f�r Steckdosen
shell_exec("/usr/local/bin/send  ".$hauscode." ".$steckdosennummer." ".$zustand);
?>