<?php


$configFile = "Config.xml";

function validAuthIP($configFile = "Config.xml")
{

    $dom = simplexml_load_file($configFile);

    $ip_address = $_SERVER['REMOTE_ADDR'];
    foreach ($dom->ipaddresses->ip as $ip) {
        if (strcmp(substr($ip_address, 0, strlen($ip)), $ip) == 0) {

            return true;
        }
    }

    return false;

}


?>