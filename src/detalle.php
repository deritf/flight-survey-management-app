<?php
require_once '../config/conexion.php';
require '../vendor/autoload.php';

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../config');
$dotenv->load();

$id = $_GET["id"] ?? $_POST["id"] ?? null;

$parametros_filtros = ['busqueda', 'fecha_busqueda', 'estado', 'busqueda_avanzada', 'origen', 'pais', 'fecha_inicio', 'fecha_fin'];
foreach ($parametros_filtros as $filtro) {
    $nombre_parametro = 'filtro_' . $filtro;
    if (isset($_GET[$nombre_parametro])) {
        $_GET[$filtro] = $_GET[$nombre_parametro];
    }
}

if (!$id) {
    die("Error: No se ha recibido un ID de vuelo.");
}

$sql = "SELECT v.num_vuelo, v.aeronave, v.codigo, v.origen, v.escala, v.ciudad_destino, v.pais_destino, v.dia_semana,
               v.opera_desde, v.opera_hasta, v.fecha, v.hora_salida, e.nombre_estado,
               COALESCE(p.bandera, '../assets/image_flags/unknown.png') AS bandera,
               COALESCE(p.abreviacion, 'DES') AS abreviacion,
               COALESCE(p.nombre_espanol, 'PAÍS DESCONOCIDO') AS nombre_espanol,
               COALESCE(p.nombre_ingles, 'UNKNOWN COUNTRY') AS nombre_ingles,
               COALESCE(p.nombre_nativo, 'UNKNOWN') AS nombre_nativo,
               ac.isla AS isla_origen, ac.codigo_iata AS iata_origen
        FROM vuelos v
        JOIN estados_vuelo e ON v.estado_id = e.id
        LEFT JOIN paises_banderas p ON BINARY v.pais_destino = BINARY p.nombre
        LEFT JOIN aeropuertos_canarias ac ON v.origen = ac.codigo_iata
        WHERE v.id = :id";

$stmt = $conexion->prepare($sql);
$stmt->execute([':id' => $id]);
$vuelo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vuelo) {
    die("Error: No se encontró el vuelo.");
}

$dias_traduccion = [
    'L' => 'Lunes',
    'M' => 'Martes',
    'X' => 'Miércoles',
    'J' => 'Jueves',
    'V' => 'Viernes',
    'S' => 'Sábado',
    'D' => 'Domingo'
];

$dias_convertidos = [];
foreach (str_split($vuelo['dia_semana']) as $letra) {
    if (isset($dias_traduccion[$letra])) {
        $dias_convertidos[] = $dias_traduccion[$letra];
    }
}
$dia_semana_traducido = implode(', ', $dias_convertidos);

$estado_clase = match (strtolower($vuelo['nombre_estado'])) {
    'activo' => 'estado-activo',
    'expirado' => 'estado-expirado',
    'encuestado' => 'estado-encuestado',
    default => 'estado-default',
};

$bandera_origen = "../assets/image_flags/canarias.png";
$title_origen = "ISLAS CANARIAS - ESPAÑA";

$busqueda = $_GET['busqueda'] ?? '';
$fecha = $_GET['fecha_busqueda'] ?? '';
$estado = $_GET['estado'] ?? '';
$busqueda_avanzada = $_GET['busqueda_avanzada'] ?? '';
$origen = $_GET['origen'] ?? '';
$pais = $_GET['pais'] ?? '';
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Vuelo</title>
    <link rel="stylesheet" href="../css/variables.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

    <?php if (isset($_GET['status'])): ?>
        <div class="notificacion fade-out <?php echo $_GET['status'] === 'success' ? 'notificacion-exito' : 'notificacion-error'; ?>">
            <span class="icono">
                <?php echo $_GET['status'] === 'success' ? '&#10004;' : '&#9888;'; ?>
            </span>
            <span class="mensaje">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </span>
            <span class="countdown"></span>
        </div>
    <?php endif; ?>

    <div class="detalle-container">
        <h1 class="ocultar">Detalle del Vuelo</h1>

        <div class="detalle-info texto-centro espacio-inferior">
            <p class="resaltar-numero-vuelo"><?php echo strtoupper(htmlspecialchars($vuelo['num_vuelo'])); ?></p>
            <p class="resaltar-aeronave"><?php echo strtoupper(htmlspecialchars($vuelo['aeronave'])); ?></p>
        </div>

        <div class="detalle-espaciado"></div>

        <div class="detalle-datos texto-izquierda">
            <p><img src="../assets/icons/codigo.png" alt="Código" title="Código del Vuelo"> <?php echo htmlspecialchars($vuelo['codigo']); ?></p>

            <p class="destino-container">
                <img src="../assets/icons/origen.png" alt="Origen" title="Origen del Vuelo">
                <span class="destino-texto"><?php echo htmlspecialchars($vuelo['iata_origen']); ?> - <?php echo htmlspecialchars($vuelo['isla_origen']); ?></span>
                <img id="bandera-destino" src="<?php echo $bandera_origen; ?>" alt="Bandera de Canarias" title="<?php echo $title_origen; ?>">
            </p>

            <p><img src="../assets/icons/escala.png" alt="Escala" title="Escala del Vuelo"> <?php echo htmlspecialchars($vuelo['escala'] ?? 'N/A'); ?></p>

            <p class="destino-container">
                <img src="../assets/icons/destino.png" alt="Destino" title="Destino del Vuelo">
                <span class="destino-texto"><?php echo htmlspecialchars($vuelo['ciudad_destino']); ?> - <?php echo htmlspecialchars($vuelo['pais_destino']); ?></span>
                <img id="bandera-destino" src="<?php echo htmlspecialchars($vuelo['bandera']); ?>" alt="Bandera de <?php echo htmlspecialchars($vuelo['nombre_espanol']); ?>" title="<?php echo htmlspecialchars($vuelo['nombre_espanol']) . ' - ' . htmlspecialchars($vuelo['nombre_ingles']) . ' - ' . htmlspecialchars($vuelo['nombre_nativo']); ?>">
            </p>

            <p><img src="../assets/icons/dia_semana.png" alt="Día de la Semana" title="Día de la Semana"> <?php echo $dia_semana_traducido; ?></p>

            <p class="resaltar-texto-sin-resaltar"><img src="../assets/icons/opera_desde.png" alt="Opera Desde" title="Fecha de Inicio de Operación"> <span class="fecha"><?php echo htmlspecialchars($vuelo['opera_desde']); ?></span></p>
            <p class="resaltar-texto-sin-resaltar"><img src="../assets/icons/opera_hasta.png" alt="Opera Hasta" title="Fecha de Fin de Operación"> <span class="fecha"><?php echo htmlspecialchars($vuelo['opera_hasta']); ?></span></p>
        </div>

        <div class="detalle-centro texto-centro resaltar-estado-fecha-hora espacio-superior">
            <p class="resaltar-estado-fecha-hora <?php echo $estado_clase; ?>">
                <?php echo strtoupper(htmlspecialchars($vuelo['nombre_estado'])); ?>
            </p>
            <p class="resaltar-estado-fecha-hora">
                <span class="fecha"><?php echo htmlspecialchars($vuelo['fecha']); ?></span>
            </p>
            <p class="resaltar-estado-fecha-hora">
                <?php echo htmlspecialchars($vuelo['hora_salida']); ?>
            </p>
        </div>

        <div class="detalle-botones alterar-orden-botones">
            <a href="#" class="boton-volver" onclick="event.preventDefault(); navegarConFiltros('listado.php')">Volver</a>
            <a href="#" class="boton-borrar" onclick="event.preventDefault(); confirmarBorrado(event, <?php echo $id; ?>);">Borrar</a>
            <a href="#" class="boton-editar" onclick="event.preventDefault(); navegarConFiltros('editar.php', <?= $id ?>)">Editar</a>
        </div>

    </div>

    <script>
        const NOTIFICACION_TIEMPO = <?php echo isset($_ENV['NOTIFICACION_TIEMPO']) ? intval($_ENV['NOTIFICACION_TIEMPO']) : 8; ?>;
    </script>

    <script src="../js/scripts.js"></script>
    <script src="../js/cuenta_atras.js"></script>
</body>
</html>