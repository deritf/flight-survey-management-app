<?php
session_start();
require_once '../config/conexion.php';

if (isset($_SESSION['usuario_id'])) {
    try {
        $stmt = $conexion->prepare("UPDATE usuarios SET ultima_conexion = NOW() WHERE id = :usuario_id");
        $stmt->execute(['usuario_id' => $_SESSION['usuario_id']]);
    } catch (PDOException $e) {
        error_log("Error al actualizar la última conexión: " . $e->getMessage());
    }
}

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

header("Location: ../public/index.php");
exit();
?>