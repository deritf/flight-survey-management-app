<?php
require_once '../config/conexion.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["estado_id"])) {
    $id = $_POST["id"];
    $estado_id = $_POST["estado_id"];

    $sql_check = "SELECT id, nombre_estado FROM estados_vuelo WHERE id = :estado_id";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([':estado_id' => $estado_id]);
    $estado = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$estado) {
        echo json_encode(["success" => false, "message" => "Estado no válido"]);
        exit;
    }

    $sql = "UPDATE vuelos SET estado_id = :estado_id WHERE id = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':estado_id' => $estado_id, ':id' => $id]);

    echo json_encode([
        "success" => true,
        "estado_id" => $estado_id,
        "estado_nombre" => $estado['nombre_estado']
    ]);
    exit();
}
?>