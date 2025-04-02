<?php
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_vuelo = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $encuestas = isset($_POST['encuestas']) ? (int)$_POST['encuestas'] : null;

    if ($id_vuelo !== null && $encuestas !== null && $encuestas >= 0 && $encuestas <= 850) {
        $sql = "UPDATE vuelos SET encuestas_realizadas = :encuestas WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':encuestas', $encuestas, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id_vuelo, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Datos inválidos"]);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>