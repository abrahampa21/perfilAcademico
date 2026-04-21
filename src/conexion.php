<?php
require_once("configuration.php");

$conexion= new mysqli($server,$user,$password,$database);

if($conexion->connect_error){
    die("Connection error: " . $conexion->connect_error);
}