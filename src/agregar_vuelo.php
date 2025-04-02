<?php
require_once '../config/conexion.php';

if (!$conexion) {
    die("Error en la conexión: " . mysqli_connect_error());
}

$sql_estados = "SELECT id, nombre_estado FROM estados_vuelo";
$stmt_estados = $conexion->prepare($sql_estados);
$stmt_estados->execute();
$estados = $stmt_estados->fetchAll(PDO::FETCH_ASSOC);

$sql_aeropuertos = "SELECT codigo_iata, isla, provincia FROM aeropuertos_canarias ORDER BY provincia ASC, isla DESC";
$stmt_aeropuertos = $conexion->prepare($sql_aeropuertos);

if ($stmt_aeropuertos->execute()) {
    $aeropuertos = $stmt_aeropuertos->fetchAll(PDO::FETCH_ASSOC);
} else {
    die("Error al ejecutar la consulta de aeropuertos.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Nuevo Vuelo</title>
    <link rel="stylesheet" href="../css/variables.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="editar-container margin-top-especial">
        <h1 class="ocultar">Agregar Nuevo Vuelo</h1>

        <form action="guardar_nuevo_vuelo.php" method="POST">
            <div class="detalle-info texto-centro espacio-inferior-40">
                <p>
                    <input type="text" name="num_vuelo" class="resaltar-numero-vuelo" placeholder="Número de vuelo"
                           required style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                </p>
                <p>
                    <input type="text" name="aeronave" class="resaltar-aeronave" placeholder="Aeronave"
                           required style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                </p>
            </div>

            <div class="detalle-datos texto-izquierda">
                <p><img src="../assets/icons/codigo.png" alt="Código" title="Código del Vuelo">
                    <input type="text" name="codigo" placeholder="Código" required
                           style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                </p>

                <p><img src="../assets/icons/origen.png" alt="Origen" title="Origen del Vuelo">
                    <select name="origen" required>
                        <option value="" disabled selected>Selecciona un origen</option>
                        <?php
                        $provincia_actual = '';
                        if (!empty($aeropuertos)):
                            foreach ($aeropuertos as $aeropuerto):
                                if ($provincia_actual != $aeropuerto['provincia']):
                                    if ($provincia_actual != '') echo '</optgroup>';
                                    $provincia_actual = $aeropuerto['provincia'];
                                    echo '<optgroup label="'.htmlspecialchars($provincia_actual).'">';
                                endif;
                                ?>
                                <option value="<?php echo htmlspecialchars($aeropuerto['codigo_iata']); ?>">
                                    <?php echo htmlspecialchars($aeropuerto['isla']); ?>
                                </option>
                            <?php endforeach;
                            echo '</optgroup>';
                        else: ?>
                            <option disabled>No hay aeropuertos disponibles</option>
                        <?php endif; ?>
                    </select>
                </p>

                <p><img src="../assets/icons/escala.png" alt="Escala" title="Escala del Vuelo (Opcional)">
                    <input type="text" name="escala" placeholder="Escala (Opcional)"
                           style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                </p>

                <p><img src="../assets/icons/destino.png" alt="Destino" title="País de Destino">
                    <input type="text" name="destino" placeholder="Destino" required
                           style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                </p>

                <p><img src="../assets/icons/icon-city.png" alt="Ciudad" title="Ciudad de Destino">
                    <input type="text" name="ciudad" placeholder="Ciudad" required
                           style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                </p>

                <p><img src="../assets/icons/dia_semana.png" alt="Día de la semana" title="Día de la Semana">
                    <input type="text" name="dia_semana" placeholder="Día de la semana (LMXJVSD)" required
                           style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                </p>

                <p><img src="../assets/icons/opera_desde.png" alt="Opera Desde" title="Fecha de Inicio de Operación">
                    <input type="date" name="opera_desde" required>
                </p>

                <p><img src="../assets/icons/opera_hasta.png" alt="Opera Hasta" title="Fecha de Fin de Operación">
                    <input type="date" name="opera_hasta" required>
                </p>
            </div>

            <div class="detalle-centro detalle-final texto-centro resaltar-estado-fecha-hora espacio-superior-30">
                <p>
                    <select name="estado_id" required>
                        <option value="" disabled selected>Selecciona un estado</option>
                        <?php foreach ($estados as $estado): ?>
                            <option value="<?php echo $estado['id']; ?>">
                                <?php echo strtoupper(htmlspecialchars($estado['nombre_estado'])); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <p><input type="date" name="fecha" required></p>
                <p><input type="time" name="hora_salida" required></p>
            </div>

            <div class="detalle-botones2">
                <button type="submit" class="boton-guardar">Añadir</button>
                <button type="button" class="boton-volver-sin-guardar" onclick="window.history.back();">Cancelar y volver</button>
            </div>
        </form>
    </div>

    <script src="../js/scripts.js"></script>
</body>
</html>