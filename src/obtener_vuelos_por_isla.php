<?php
header('Content-Type: application/json');
require_once '../config/conexion.php';

try {
    if (!isset($_GET['isla']) || empty(trim($_GET['isla']))) {
        echo json_encode(["error" => "Isla no seleccionada"]);
        exit;
    }

    $isla = trim($_GET['isla']);

    $sql = "SELECT v.pais_destino AS pais, COUNT(*) AS cantidad
            FROM vuelos v
            JOIN aeropuertos_canarias ac ON v.origen = ac.codigo_iata
            WHERE ac.isla = :isla
            GROUP BY v.pais_destino
            ORDER BY cantidad DESC
            LIMIT 8";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':isla', $isla, PDO::PARAM_STR);
    $stmt->execute();

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($resultados)) {
        echo json_encode(["error" => "No se encontraron datos para la isla seleccionada."]);
        exit;
    }

    echo json_encode($resultados);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(["error" => "Error inesperado: " . $e->getMessage()]);
}
?>