<?php
 
function getSystemInfo(){
    $systeminfo = array();
     
    //Systemlaufzeit abfragen
    $uptime = exec("uptime");
     
    //Rückgabestring zurechtschneiden
    $uptime = preg_replace("/[0-9][0-9]:[0-9][0-9]:[0-9][0-9] up/", "", $uptime);
    $uptime = preg_replace("/,[ ]*.[ ]user.*/", "", $uptime);
    $uptime = ltrim($uptime, " ");
     
    array_push($systeminfo, array('name' => 'Laufzeit', 'type' => 'uptime', 'value' => $uptime));
     
    //CPU-Temperatur abfragen und auf Zehntel-Grad runden
    exec("cat /sys/class/thermal/thermal_zone0/temp", $cputemp);
    $cputemp = round($cputemp[0] / 1000, 1);
     
    array_push($systeminfo, array('name' => 'CPU-Temp', 'type' => 'cputemp', 'value' => $cputemp.' &deg;C'));
     
    //Kernelversion abfragen
    $kernel = exec("uname -r");
     
    array_push($systeminfo, array('name' => 'Kernel', 'type' => 'kernel', 'value' => $kernel));
     
    //Speichernutzung abfragen
    exec("df --output=used,size", $spaceinfo);
     
    //erstes Element wird übersprungen, da es nur Text enthält
    for($i = 1; $i < sizeOf($spaceinfo); $i++){
        $info = trim($spaceinfo[$i]);
        $info = preg_replace('!\s+!', ' ', $info);
         
        $spacearray = explode(" ", $info);
         
        $usedspace += $spacearray[0];
        $totalspace += $spacearray[1];
    }
     
    //Benutzten Speicherplatz in Prozent auf ganze Zahlen runden
    $spaceusagepercent = round(($usedspace / $totalspace)*100)."%";
     
    array_push($systeminfo, array('name' => 'Speichernutzung', 'type' => 'spaceusage', 'value' => $spaceusagepercent));
     
    //Freien Speicherplatz berechnen
    $freespace = $totalspace - $usedspace;
     
    //Zählen, wie oft freier Speicher durch 1024 geteilt werden kann
    while($freespace >= 1024){
        $counter++;
        $freespace /= 1024;
    }
     
    //Anhand der Anzahl der durchgeführten Divisionen Größenordnung der Nachkommastellen bestimmen
    switch($counter){
        case 1:
            $sizetext = " Mb";
            $decimal = 1;
            break;
        case 2:
            $sizetext = " GB";
            $decimal = 1;
            break;
        case 3:
            $sizetext = " TB";
            $decimal = 2;
            break;
        default:
            $sizetext = " kb";
            $decimal = 0;
            break;
    }
     
    //Freien Speicherplatz angemessen runden (GB und Mb auf 1 Nachkommastelle, TB auf 2, kb garnicht runden)
    $freespace = round($freespace, $decimal);
    array_push($systeminfo, array('name' => 'Freier Speicher', 'type' => 'freespace', 'value' => $freespace.$sizetext));
     
    //Arbeitsspeicher-Info abfragen
    exec("free", $raminfo);
     
    //RAM-Auslastung aus Ausgabe herauslesen
    $info = trim($raminfo[1]);
    $info = preg_replace('!\s+!', ' ', $info);
    $ramarray = explode(" ", $info);
     
    $totalram = $ramarray[1];
    $usedram = $ramarray[2];
     
    //RAM-Auslastung in Prozent auf ganze Zahlen runden
    $ramusage = round(($usedram / $totalram)*100)."%";
     
    array_push($systeminfo, array('name' => 'RAM-Nutzung', 'type' => 'ramusage', 'value' => $ramusage));
     
    //Firmwareversion des Betriebssystems abfragen
    //Die Versionsinfo ist ein Array mit 3 Werten: Erscheinungsdatum, Copyright-Hinweis, Versionsnummer
    exec("sudo /opt/vc/bin/vcgencmd version", $firmwareversion);
     
    //Ausgegeben wird nur das erste Element mit dem Erscheinungsdatum der Versionsinfo
    $firmwareversion = $firmwareversion[0];
     
    array_push($systeminfo, array('name' => 'Firmware', 'type' => 'firmware', 'value' => $firmwareversion));
     
    //JSON-Objekt erstellen und zurückgeben
    return json_encode(array('systeminfo' => $systeminfo));
}
 
?>