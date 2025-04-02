<?php
require_once '../config/conexion.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'], $data['estado'])) {
    $id = (int)trim($data['id']);
    $estado = (int)trim($data['estado']);

    $stmt = $conexion->prepare("UPDATE vuelos SET estado_id = :estado WHERE id = :id");
    $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
}