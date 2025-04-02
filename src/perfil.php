<?php
require_once '../config/conexion.php';
require '../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../config');
$dotenv->load();

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../public/login.php");
    exit();
}

$id = $_SESSION['usuario_id'];

$stmt = $conexion->prepare("SELECT nombre, apellido1, apellido2, email, fecha_creacion, ultima_conexion FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Error: No se encontró el usuario.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="../css/variables.css">
    <link rel="stylesheet" href="../css/perfil.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="perfil-container">
        <h1>Perfil de Usuario</h1>
        <div class="perfil-datos">
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></p>
            <p><strong>Apellido 1:</strong> <?php echo htmlspecialchars($usuario['apellido1']); ?></p>
            <p><strong>Apellido 2:</strong> <?php echo htmlspecialchars($usuario['apellido2']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
            <p><strong>Fecha de Creación:</strong> <?php echo htmlspecialchars($usuario['fecha_creacion']); ?></p>
            <p><strong>Última Conexión:</strong> <?php echo htmlspecialchars($usuario['ultima_conexion'] ?? 'Nunca'); ?></p>
        </div>

        <a href="listado.php" class="boton-volver-sin-guardar">Volver</a>
    </div>
</body>
</html>