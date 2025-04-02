<?php
require_once '../config/conexion.php';

if (!isset($_GET['fecha']) || empty($_GET['fecha'])) {
    echo json_encode(["error" => "Fecha no seleccionada"]);
    exit;
}

$fecha = $_GET['fecha'];

$sql = "SELECT ac.isla, COUNT(v.id) as cantidad_vuelos
        FROM vuelos v
        JOIN aeropuertos_canarias ac ON v.origen = ac.codigo_iata
        WHERE v.fecha = :fecha
        GROUP BY ac.isla
        ORDER BY cantidad_vuelos DESC";

$stmt = $conexion->prepare($sql);
$stmt->bindValue(':fecha', $fecha, PDO::PARAM_STR);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($resultados);
?>