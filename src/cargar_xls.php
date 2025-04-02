<?php
require_once '../config/conexion.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (php_sapi_name() !== 'cli') {
    ini_set('max_input_vars', '8000');
    ini_set('memory_limit', '1024M');
    set_time_limit(600);
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["archivo_xls"])) {
    $file = $_FILES["archivo_xls"]["tmp_name"];
    $fileName = $_FILES["archivo_xls"]["name"];
    $fila_inicio = isset($_POST["fila_inicio"]) && $_POST["fila_inicio"] !== '' ? intval($_POST["fila_inicio"]) : 1;
    $fila_fin = isset($_POST["fila_fin"]) && $_POST["fila_fin"] !== '' ? intval($_POST["fila_fin"]) : PHP_INT_MAX;

    $isExportedFile = str_starts_with($fileName, "vuelos_exp_");

    if ($fila_inicio > $fila_fin) {
        header("Location: listado.php?status=error&message=La+fila+de+inicio+no+puede+ser+mayor+que+la+fila+de+fin.");
        exit;
    }

    if (!$file) {
        header("Location: listado.php?status=error&message=No+se+ha+subido+ningún+archivo.");
        exit;
    }

    try {
        $conexion->exec("DELETE FROM vuelos");
        $conexion->exec("ALTER TABLE vuelos AUTO_INCREMENT = 1");

        $spreadsheet = IOFactory::load($file);
        $batchSize = 100;
        $contador = 0;
        $total_insertadas = 0;
        $insertBatch = [];

        foreach ($spreadsheet->getSheetNames() as $index => $nombreHoja) {
            $hoja = $spreadsheet->getSheet($index);
            $datos = $hoja->toArray(null, true, true, true);

            if (empty($datos)) {
                continue;
            }

            foreach ($datos as $fila) {
                $contador++;
                if ($contador < $fila_inicio) continue;
                if ($contador > $fila_fin) break;
                if ($contador === 1) continue;

                $obs = trim($fila['A'] ?? '');
                $hora_salida = isset($fila['B']) ? date("H:i:s", strtotime($fila['B'])) : null;
                $origen = strtoupper(trim($fila['C'] ?? ''));
                $dia_semana = strtoupper(str_replace(' ', '', trim($fila['D'] ?? '')));
                $codigo = strtoupper(trim($fila['E'] ?? ''));
                $ciudad_destino = strtoupper(trim($fila['F'] ?? ''));
                $pais_destino = strtoupper(trim($fila['G'] ?? ''));
                $escala = strtoupper(trim($fila['H'] ?? '')) ?: 'N/A';
                $aeronave = strtoupper(trim($fila['I'] ?? ''));
                $num_vuelo = strtoupper(trim($fila['J'] ?? ''));
                $opera_desde = convertirFecha($fila['K'] ?? '');
                $opera_hasta = convertirFecha($fila['L'] ?? '');
                $fecha = convertirFecha($fila['M'] ?? '');
                $encuestas_realizadas = isset($fila['N']) && is_numeric($fila['N']) ? intval($fila['N']) : 0;
                $estado_id = $isExportedFile
                    ? (isset($fila['O']) && is_numeric($fila['O']) ? intval($fila['O']) : 1)
                    : 1;

                if (!$obs || !$num_vuelo || !$fecha) {
                    continue;
                }

                $insertBatch[] = [
                    'obs' => strtoupper($obs),
                    'hora_salida' => $hora_salida,
                    'origen' => $origen,
                    'dia_semana' => $dia_semana,
                    'codigo' => $codigo,
                    'ciudad_destino' => $ciudad_destino,
                    'pais_destino' => $pais_destino,
                    'escala' => $escala,
                    'aeronave' => $aeronave,
                    'num_vuelo' => $num_vuelo,
                    'opera_desde' => $opera_desde,
                    'opera_hasta' => $opera_hasta,
                    'fecha' => $fecha,
                    'encuestas_realizadas' => $encuestas_realizadas,
                    'estado_id' => $estado_id
                ];

                if (count($insertBatch) >= $batchSize) {
                    insertarLote($conexion, $insertBatch);
                    $total_insertadas += count($insertBatch);
                    $insertBatch = [];
                }
            }
        }

        if (!empty($insertBatch)) {
            insertarLote($conexion, $insertBatch);
            $total_insertadas += count($insertBatch);
        }

        header("Location: listado.php?status=success&message=Carga+de+datos+completada+con+éxito.+Total+registros:+$total_insertadas");
    } catch (Exception $e) {
        error_log("Error al procesar el archivo: " . $e->getMessage(), 3, "../logs/error.log");
        header("Location: listado.php?status=error&message=Error+al+procesar+el+archivo.+Revisa+el+archivo+logs/error.log");
    }

    exit;
}

function insertarLote($conexion, $lote) {
    $sql = "INSERT INTO vuelos (
                obs, hora_salida, origen, dia_semana, codigo,
                ciudad_destino, pais_destino, escala, aeronave, num_vuelo,
                opera_desde, opera_hasta, fecha, encuestas_realizadas, estado_id
            ) VALUES (
                :obs, :hora_salida, :origen, :dia_semana, :codigo,
                :ciudad_destino, :pais_destino, :escala, :aeronave, :num_vuelo,
                :opera_desde, :opera_hasta, :fecha, :encuestas_realizadas, :estado_id
            )";

    $stmt = $conexion->prepare($sql);
    foreach ($lote as &$fila) {
        $stmt->execute($fila);
    }
}

function convertirFecha($fecha) {
    if (!$fecha) return null;

    $formatos = ['d-m-y', 'd-m-Y', 'd/m/Y', 'Y-m-d', 'dMY', 'd-M-y'];
    foreach ($formatos as $formato) {
        $dt = DateTime::createFromFormat($formato, $fecha);
        if ($dt !== false) return $dt->format('Y-m-d');
    }

    return null;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Archivo XLS</title>
    <link rel="stylesheet" href="../css/variables.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/cargar_xls.css">
</head>
<body>

    <main class="detalle-container cargar_xls">
        <h1 class="ocultar">Carga de Archivo XLS</h1>

        <form class="formulario-carga" action="cargar_xls.php" method="post" enctype="multipart/form-data">

            <fieldset class="texto-izquierda">
                <label for="archivo_xls">Selecciona el archivo .xls:</label>
                <label for="archivo_xls" class="custom-file-label" id="label-archivo">
                    <span id="texto-archivo">Pulsa aquí para elegir archivo</span>
                    <input type="file" name="archivo_xls" id="archivo_xls" required>
                </label>
            </fieldset>

            <fieldset class="texto-izquierda">
                <label for="fila_inicio">Fila de inicio:</label>
                <input type="number" name="fila_inicio" id="fila_inicio" min="1" value="1">
            </fieldset>

            <fieldset class="texto-izquierda">
                <label for="fila_fin">Fila de fin:</label>
                <input type="number" name="fila_fin" id="fila_fin" min="1">
            </fieldset>

            <footer class="texto-centro espacio-superior-30 botones-carga">
                <a href="listado.php" class="boton-volver">Volver</a>
                <button type="submit" class="boton-editar">Subir y Procesar</button>
            </footer>
        </form>
    </main>

    <script src="../js/scripts.js"></script>
</body>
</html>