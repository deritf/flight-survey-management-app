<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <link rel="stylesheet" href="../css/variables.css">
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>

<div class="container">
    <img src="../assets/logo/Logo-SEC-largo.png" alt="Logo" class="logo">

    <p class="intro-text">
        Bienvenido a la plataforma de gestión de vuelos. Aquí podrás visualizar, gestionar y actualizar la información de los vuelos disponibles.
    </p>

    <?php if (!isset($_SESSION['usuario_id'])): ?>
        <div class="button-group">
            <a href="login.php" class="btn btn-login">Iniciar sesión</a>
            <a href="signup.php" class="btn btn-register">Registrarse</a>
        </div>
    <?php else: ?>
        <p class="welcome-text">¡Hola de nuevo, <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?>!</p>
        <div class="button-group">
            <a href="../src/listado.php" class="btn btn-dashboard">Entrar</a>
            <a href="logout.php" class="btn btn-logout">Cerrar Sesión</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>