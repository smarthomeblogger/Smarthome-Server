<?php

$hauscode = $_GET['hauscode']; //Der Hauscode der Steckdose 
$steckdosennummer = $_GET['nummer']; //Die Steckdosennummer [Zahl von 1-5]
$zustand = $_GET['zustand']; //Gewünschter Zustand [1 für an; 0 für aus]

//Schaltbefehl für Steckdosen
shell_exec("/usr/local/bin/send  ".$hauscode." ".$steckdosennummer." ".$zustand);
?>