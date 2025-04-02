<?php
require_once '../config/conexion.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    function validarNombre($nombre) {
        return strlen($nombre) >= 4;
    }

    function validarEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    function validarPasswords($pass1, $pass2) {
        return ($pass1 === $pass2 && strlen($pass1) > 5);
    }

    $nombre = trim($_POST['nombre'] ?? '');
    $apellido1 = trim($_POST['apellido1'] ?? '');
    $apellido2 = trim($_POST['apellido2'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $password2 = trim($_POST['password2'] ?? '');

    $response = [];
    $error = false;

    if (!validarNombre($nombre)) {
        $response['errNombre'] = "El nombre debe tener al menos 4 caracteres.";
        $error = true;
    }

    if (!validarEmail($email)) {
        $response['errEmail'] = "La dirección de email no es válida.";
        $error = true;
    }

    if (!validarPasswords($password, $password2)) {
        $response['errPass'] = "Las contraseñas deben coincidir y tener al menos 6 caracteres.";
        $error = true;
    }

    if (!$error) {
        try {
            $stmt = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE nombre = :nombre OR email = :email");
            $stmt->execute(['nombre' => $nombre, 'email' => $email]);

            if ($stmt->fetchColumn() > 0) {
                $response['errUsu'] = "El usuario o el email ya están registrados.";
                $response['success'] = false;
            } else {
                $passwordCifrada = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conexion->prepare("
                    INSERT INTO usuarios (nombre, apellido1, apellido2, password, email, fecha_creacion)
                    VALUES (:nombre, :apellido1, :apellido2, :password, :email, NOW())
                ");

                $stmt->execute([
                    'nombre' => $nombre,
                    'apellido1' => $apellido1,
                    'apellido2' => $apellido2,
                    'password' => $passwordCifrada,
                    'email' => $email
                ]);

                $response['success'] = true;
                $response['message'] = "Registro completado exitosamente.";
            }
        } catch (PDOException $e) {
            $response['success'] = false;
            $response['message'] = "Error interno: " . $e->getMessage();
        }
    } else {
        $response['success'] = false;
    }

    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
    <link rel="stylesheet" href="../css/variables.css">
    <link rel="stylesheet" href="../css/login-signup.css">
    <script src="../js/signup.js" defer></script>
</head>
<body>

<main class="login-container">
    <section class="login-card">
        <header>
            <h3>Registro</h3>
        </header>

        <form class="login-signup" id="miForm" method="POST" onsubmit="event.preventDefault(); enviarFormulario();">

            <input type="text" id="nombre" name="nombre" placeholder="Nombre" required>
            <span id="errNombre" class="text-danger"></span>

            <input type="text" id="apellido1" name="apellido1" placeholder="Primer Apellido">

            <input type="text" id="apellido2" name="apellido2" placeholder="Segundo Apellido">

            <input type="email" id="email" name="email" placeholder="Correo Electrónico" required>
            <span id="errEmail" class="text-danger"></span>

            <input type="password" id="password" name="password" placeholder="Contraseña" required>

            <input type="password" id="password2" name="password2" placeholder="Confirmar Contraseña" required>
            <span id="errPass" class="text-danger"></span>

            <nav class="button-group-login">
                <a href="index.php" class="btn btn-secondary">Volver</a>
                <input type="submit" value="Registrar" class="btn btn-primary">
            </nav>


        </form>
    </section>
</main>

</body>
</html>