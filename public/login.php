<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/conexion.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: ../src/listado.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreUsuario = trim($_POST['usu'] ?? '');
    $password = trim($_POST['pass1'] ?? '');

    $response = ['success' => false, 'message' => ''];

    if (empty($nombreUsuario) || empty($password)) {
        $response['message'] = 'Usuario y contraseña son obligatorios.';
    } else {
        try {
            $stmt = $conexion->prepare("
                SELECT id, nombre, password
                FROM usuarios
                WHERE nombre = :nombre
            ");
            $stmt->execute(['nombre' => $nombreUsuario]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $conexion->prepare("UPDATE usuarios SET ultima_conexion = NOW() WHERE id = :id")
                         ->execute(['id' => $user['id']]);

                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['nombre_usuario'] = $user['nombre'];

                $response['success'] = true;
                $response['redirect'] = '../src/listado.php';
            } else {
                $response['message'] = 'Usuario o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Error interno: ' . $e->getMessage();
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <script src="../js/login.js" defer></script>
    <link rel="stylesheet" href="../css/variables.css">
    <link rel="stylesheet" href="../css/login-signup.css">
</head>
<body>
<main class="login-container">
    <section class="login-card">
        <header>
            <h3>Iniciar sesión</h3>
        </header>
        <form class="login-signup" id="miForm" method="POST" onsubmit="event.preventDefault(); enviarFormulario();">
            <input type="text" id="usu" name="usu" placeholder="Usuario" required>
            <input type="password" id="pass1" name="pass1" placeholder="Contraseña" required>

            <nav class="button-group-login">
                <a href="index.php" class="btn btn-secondary">Volver</a>
                <input type="submit" value="Entrar" class="btn btn-primary">
            </nav>

        </form>
    </section>
</main>
</body>
</html>