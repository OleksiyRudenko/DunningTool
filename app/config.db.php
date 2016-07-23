<?php
/**
 * Created by PhpStorm.
 * User: Rudenko
 * Date: 18/07/2016
 * Time: 13:59
 */

$DBconfig = [
    'host'      =>  'localhost',
    'user'      =>  'root',
    'pwd'       =>  'usbw',
    'dbname'    =>  'dunning',
    'port'      =>  3307,
];

function DBconnect($DBconfig) {
    $dbh = new mysqli($DBconfig['host'],$DBconfig['user'],$DBconfig['pwd'],$DBconfig['dbname'],$DBconfig['port']);
    if ($dbh->connect_errno) {
        // try creating db and reconnect
        logMessage('DBinit',"Failed to connect to MySQL: (" . $dbh->connect_errno . ") " . $dbh->connect_error
            .'<br/>Please, create database via admin panel','danger');
    } else {
        // logMessage('DBinit',"Connected to MySQL: " . var_export($dbh,true),'success');
    }
    return $dbh;
}