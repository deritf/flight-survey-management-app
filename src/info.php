<?php
require_once '../config/conexion.php';

$colores = [
    // Colores de la gráfica 1:
    'activos' => '#E0E0E0',
    'encuestados' => '#00ff00',
    'expirados' => '#ff0000',
    // Colores de la gráfica 2:
    'barras_islas' => '#007bff',
    // Colores de la gráfica 3:
    'barras_dias' => '#007bff',
    // Colores de la gráfica 4:
    'barras_destinos' => '#007bff'
];

$sql_estados = "SELECT estado_id, COUNT(*) as cantidad FROM vuelos GROUP BY estado_id";
$stmt_estados = $conexion->prepare($sql_estados);
$stmt_estados->execute();
$resultados_estados = $stmt_estados->fetchAll(PDO::FETCH_ASSOC);

$estados_vuelos = [
    1 => ['nombre' => 'Activos', 'color' => $colores['activos']],
    2 => ['nombre' => 'Encuestados', 'color' => $colores['encuestados']],
    3 => ['nombre' => 'Expirados', 'color' => $colores['expirados']],
];

$data_estados = [];
foreach ($resultados_estados as $fila) {
    $estado_id = $fila['estado_id'];
    if (isset($estados_vuelos[$estado_id])) {
        $data_estados[] = [
            'value' => $fila['cantidad'],
            'name' => $estados_vuelos[$estado_id]['nombre'],
            'itemStyle' => ['color' => $estados_vuelos[$estado_id]['color']]
        ];
    }
}

$sql_islas = "SELECT ac.isla, COUNT(v.id) as cantidad_vuelos
              FROM vuelos v
              JOIN aeropuertos_canarias ac ON v.origen = ac.codigo_iata
              GROUP BY ac.isla
              ORDER BY cantidad_vuelos DESC";

$stmt_islas = $conexion->prepare($sql_islas);
$stmt_islas->execute();
$vuelos_por_isla = $stmt_islas->fetchAll(PDO::FETCH_ASSOC);

$etiquetas_islas = [];
$valores_islas = [];
foreach ($vuelos_por_isla as $fila) {
    $etiquetas_islas[] = $fila['isla'];
    $valores_islas[] = $fila['cantidad_vuelos'];
}

$sql_islas_selector = "SELECT DISTINCT isla FROM aeropuertos_canarias";
$stmt_islas_selector = $conexion->prepare($sql_islas_selector);
$stmt_islas_selector->execute();
$islas = $stmt_islas_selector->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información de Vuelos</title>
    <link rel="stylesheet" href="../css/variables.css">
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>

    <script>
        var estadosData = <?php echo json_encode($data_estados); ?>;
        var islasLabels = <?php echo json_encode($etiquetas_islas); ?>;
        var islasValues = <?php echo json_encode($valores_islas); ?>;

        var colores = {
            barrasIslas: "<?php echo $colores['barras_islas']; ?>",
            barrasDias: "<?php echo $colores['barras_dias']; ?>",
            barrasDestinos: "<?php echo $colores['barras_destinos']; ?>"
        };
    </script>

    <script src="../js/graficas.js"></script>
</head>

<body>

    <div class="info-container">
        <h1>Estadísticas de Vuelos</h1>

        <div class="grafica-container">
            <h2>Distribución de Vuelos por Estado</h2>
            <div id="grafica-vuelos-estados" class="grafico"></div>
        </div>

        <div class="grafica-container">
            <h2>Distribución de Vuelos por Isla de Origen</h2>
            <div id="grafica-vuelos-islas" class="grafico"></div>
        </div>

        <div class="grafica-container">
            <h2>Vuelos por País de Destino según la Isla</h2>
            <label for="selector-isla">Selecciona una Isla:</label>
            <select id="selector-isla" onchange="cargarDatosPorIsla()">
                <option value="">-- Selecciona una isla --</option>
                <?php foreach ($islas as $isla): ?>
                    <option value="<?php echo htmlspecialchars($isla); ?>"><?php echo htmlspecialchars($isla); ?></option>
                <?php endforeach; ?>
            </select>
            <div id="grafica-destinos" class="grafico"></div>
        </div>

        <div class="grafica-container">
            <h2>Vuelos por Origen en un Día Específico</h2>
            <label for="selector-fecha">Selecciona un Día:</label>
            <input type="date" id="selector-fecha" onchange="cargarDatosPorDia()">
            <div id="grafica-origenes-dia" class="grafico"></div>
        </div>

        <div class="boton-volver-container2">
            <button type="button" class="boton-volver" onclick="window.history.back();">Volver</button>
        </div>
    </div>

</body>

</html>