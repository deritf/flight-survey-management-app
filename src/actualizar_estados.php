<?php
require_once '../config/conexion.php';

$fecha_ficticia = null; // Cambia esto para probar con diferentes fechas, o pon null para usar la real.
/*
$fecha_ficticia = '2025-01-12'; OPCION 1: Fecha simulada (para hacer pruebas internas de uso)
$fecha_ficticia = null; OPCION 2: Usar fecha del sistema (fecha actual, se supone).
*/

$fecha_actual = $fecha_ficticia ?? date('Y-m-d');

try {
    $sql = "UPDATE vuelos SET estado_id = 3 WHERE estado_id = 1 AND fecha < :fecha_actual";
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':fecha_actual', $fecha_actual, PDO::PARAM_STR);
    $stmt->execute();

    $filas_actualizadas = $stmt->rowCount();

    echo json_encode([
        "mensaje" => "Se actualizaron $filas_actualizadas vuelos a estado 'Expirado' con fecha de referencia: $fecha_actual."
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la actualización: " . $e->getMessage()]);
}
?>
