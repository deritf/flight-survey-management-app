<?php
$host = 'localhost';
$dbname = 'aplicacion_vuelos_sec';
$user = 'root';
$password = 'adminadmin';

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
    die();
}