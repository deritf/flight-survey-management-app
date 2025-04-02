<?php
require_once '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['estado'])) {
    $estado = $_POST['estado'];

    try {
        if ($estado === 'todos') {
            $sql = "DELETE FROM vuelos";
            $mensaje = "Se han eliminado TODOS los vuelos de la base de datos.";
        } else {
            $sql = "DELETE FROM vuelos WHERE estado_id = :estado";
            $mensaje = "Se han eliminado todos los vuelos con el estado seleccionado.";
        }

        $stmt = $conexion->prepare($sql);

        if ($estado !== 'todos') {
            $stmt->bindValue(':estado', $estado, PDO::PARAM_INT);
        }

        $stmt->execute();

        header("Location: listado.php?status=success&message=" . urlencode($mensaje));
        exit;
    } catch (PDOException $e) {
        $errorMessage = "Error al eliminar los vuelos: " . $e->getMessage();
        header("Location: listado.php?status=error&message=" . urlencode($errorMessage));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Datos</title>
    <link rel="stylesheet" href="../css/variables.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="detalle-container">
        <h1 class="texto-centro">Eliminar Datos de Vuelos</h1>

        <p class="texto-centro">Selecciona una opción para eliminar vuelos de la base de datos:</p>

        <div class="detalle-botones-eliminar-datos-de-vuelos">
            <button class="boton-borrar" onclick="confirmarEliminacion(3, 'Expirados')">Eliminar Vuelos Expirados</button>
            <button class="boton-borrar" onclick="confirmarEliminacion(1, 'Activos')">Eliminar Vuelos Activos</button>
            <button class="boton-borrar" onclick="confirmarEliminacion(2, 'Encuestados')">Eliminar Vuelos Encuestados</button>
            <button class="boton-borrar boton-destacado" onclick="confirmarEliminacion('todos', 'Todos')">Eliminar TODOS los Vuelos</button>
        </div>

        <button type="button" class="boton-volver" onclick="window.history.back();">Volver</button>
    </div>

    <script>
        function confirmarEliminacion(estado, tipo) {
            let mensaje = estado === 'todos'
                ? "⚠️ Esto eliminará TODOS los vuelos de la base de datos de forma permanente. ¿Estás seguro?"
                : `Se eliminarán todos los vuelos con estado '${tipo}'. Esta acción no se puede deshacer. ¿Confirmas la eliminación?`;

            if (confirm(mensaje)) {
                const form = document.createElement("form");
                form.method = "POST";
                form.action = "eliminar_datos.php";

                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "estado";
                input.value = estado;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
<script src="../js/notificaciones.js"></script>
</body>
</html>