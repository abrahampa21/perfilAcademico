<?php
require_once("configuration.php");

//Aquí se ponen las variables de las credenciales de acceso a la base de datos (configuration.php) 
$connection = new mysqli($server,$user,$password,$database);

if($connection->connect_error){
    die("Connection error: " . $connection->connect_error);
}