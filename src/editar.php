<?php
require_once '../config/conexion.php';

if (!isset($_GET["id"])) {
    die("Error: No se ha recibido un ID de vuelo.");
}

$id = $_GET["id"];

$sql = "SELECT v.num_vuelo, v.aeronave, v.codigo, v.origen, v.escala, v.pais_destino, v.ciudad_destino, v.dia_semana,
               v.opera_desde, v.opera_hasta, v.fecha, v.hora_salida, v.estado_id, e.nombre_estado
        FROM vuelos v
        JOIN estados_vuelo e ON v.estado_id = e.id
        WHERE v.id = :id";
$stmt = $conexion->prepare($sql);
$stmt->execute([':id' => $id]);
$vuelo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vuelo) {
    die("Error: No se encontró el vuelo.");
}

$sql_estados = "SELECT id, nombre_estado FROM estados_vuelo";
$stmt_estados = $conexion->prepare($sql_estados);
$stmt_estados->execute();
$estados = $stmt_estados->fetchAll(PDO::FETCH_ASSOC);

$sql_aeropuertos = "SELECT codigo_iata, isla, provincia FROM aeropuertos_canarias ORDER BY provincia ASC, isla DESC";
$stmt_aeropuertos = $conexion->prepare($sql_aeropuertos);
$stmt_aeropuertos->execute();
$aeropuertos = $stmt_aeropuertos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Vuelo</title>
    <link rel="stylesheet" href="../css/variables.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="editar-container margin-top-especial">
        <h1 class="ocultar">Editar Vuelo</h1>

        <form action="guardar_edicion.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

            <div class="detalle-info texto-centro espacio-inferior-40">
                <p>
                    <input type="text" name="num_vuelo" class="resaltar-numero-vuelo"
                        value="<?php echo strtoupper(htmlspecialchars($vuelo['num_vuelo'])); ?>"
                        required style="text-transform: uppercase;"
                        oninput="this.value = this.value.toUpperCase();">
                </p>
                <p>
                    <input type="text" name="aeronave" class="resaltar-aeronave"
                        value="<?php echo strtoupper(htmlspecialchars($vuelo['aeronave'])); ?>"
                        required style="text-transform: uppercase;"
                        oninput="this.value = this.value.toUpperCase();">
                </p>
            </div>

            <div class="detalle-datos texto-izquierda">
                <p><img src="../assets/icons/codigo.png" alt="Código" title="Código del Vuelo">
                    <input type="text" name="codigo" value="<?php echo htmlspecialchars($vuelo['codigo']); ?>"
                           required style="text-transform: uppercase;"
                           oninput="this.value = this.value.toUpperCase();">
                </p>

                <p><img src="../assets/icons/origen.png" alt="Origen" title="Origen del Vuelo">
                    <select name="origen" required>
                        <option value="" disabled>Selecciona un origen</option>
                        <?php
                        $provincia_actual = '';
                        foreach ($aeropuertos as $aeropuerto):
                            if ($provincia_actual != $aeropuerto['provincia']):
                                if ($provincia_actual != '') echo '</optgroup>';
                                $provincia_actual = $aeropuerto['provincia'];
                                echo '<optgroup label="'.htmlspecialchars($provincia_actual).'">';
                            endif;
                        ?>
                            <option value="<?php echo htmlspecialchars($aeropuerto['codigo_iata']); ?>"
                                <?php echo ($aeropuerto['codigo_iata'] == $vuelo['origen']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($aeropuerto['isla']); ?>
                            </option>
                        <?php endforeach; ?>
                        <?php echo '</optgroup>'; ?>
                    </select>
                </p>

                <p><img src="../assets/icons/escala.png" alt="Escala" title="Escala del Vuelo (Opcional)">
                    <input type="text" name="escala" value="<?php echo htmlspecialchars($vuelo['escala'] ?? ''); ?>"
                           style="text-transform: uppercase;"
                           oninput="this.value = this.value.toUpperCase();">
                </p>

                <p><img src="../assets/icons/destino.png" alt="Destino" title="País de Destino">
                    <input type="text" name="destino" value="<?php echo htmlspecialchars($vuelo['pais_destino']); ?>"
                            required style="text-transform: uppercase;"
                            oninput="this.value = this.value.toUpperCase();">
                </p>

                <p><img src="../assets/icons/icon-city.png" alt="Ciudad" title="Ciudad de Destino">
                <input type="text" name="ciudad" value="<?php echo htmlspecialchars($vuelo['ciudad_destino']); ?>"
                            required style="text-transform: uppercase;"
                            oninput="this.value = this.value.toUpperCase();">
                </p>

                <p>
                    <img src="../assets/icons/dia_semana.png" alt="Día de la semana" title="Día de la Semana">
                    <input type="text"
                        name="dia_semana"
                        value="<?php echo htmlspecialchars($vuelo['dia_semana']); ?>"
                        required
                        style="text-transform: uppercase;"
                        oninput="this.value = this.value.replace(/\s/g, '').toUpperCase();">
                </p>

                <p><img src="../assets/icons/opera_desde.png" alt="Opera Desde" title="Fecha de Inicio de Operación">
                    <input type="date" name="opera_desde" value="<?php echo htmlspecialchars($vuelo['opera_desde']); ?>" required>
                </p>

                <p><img src="../assets/icons/opera_hasta.png" alt="Opera Hasta" title="Fecha de Fin de Operación">
                    <input type="date" name="opera_hasta" value="<?php echo htmlspecialchars($vuelo['opera_hasta']); ?>" required>
                </p>
            </div>

            <div class="detalle-centro detalle-final texto-centro resaltar-estado-fecha-hora espacio-superior-30">
                <p>
                    <select name="estado_id" required>
                        <?php foreach ($estados as $estado): ?>
                            <option value="<?php echo $estado['id']; ?>"
                                <?php echo ($estado['id'] == $vuelo['estado_id']) ? 'selected' : ''; ?>>
                                <?php echo strtoupper(htmlspecialchars($estado['nombre_estado'])); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <p><input type="date" name="fecha" value="<?php echo htmlspecialchars($vuelo['fecha']); ?>" required></p>
                <p><input type="time" name="hora_salida" value="<?php echo htmlspecialchars($vuelo['hora_salida']); ?>" required></p>
            </div>

            <?php
            $filtros = ['busqueda', 'fecha_busqueda', 'estado', 'busqueda_avanzada', 'origen', 'pais', 'fecha_inicio', 'fecha_fin'];
            foreach ($filtros as $filtro) {
                if (isset($_GET[$filtro])) {
                    echo '<input type="hidden" name="filtro_' . htmlspecialchars($filtro) . '" value="' . htmlspecialchars($_GET[$filtro]) . '">' . "\n";
                }
            }
            ?>

            <div class="detalle-botones2">
                <button type="submit" class="boton-guardar">Guardar</button>
                <button type="button" class="boton-volver-sin-guardar" onclick="window.history.back();">Volver sin guardar</button>
            </div>
        </form>
    </div>

    <script src="../js/scripts.js"></script>
</body>
</html>