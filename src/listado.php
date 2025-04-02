<?php
session_start();
require_once '../config/conexion.php';
require '../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../config');
$dotenv->load();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../public/login.php");
    exit();
}
$nombreUsuario = $_SESSION['nombre_usuario'];

$fecha_ficticia = null; // Cambia esto para probar con diferentes fechas, o pon null para usar la real.
// $fecha_ficticia = '2025-01-12'; OPCION 1: Fecha simulada (para hacer pruebas internas de uso)
// $fecha_ficticia = null; OPCION 2: Usar fecha del sistema (fecha actual, se supone).

$fecha_actual = $fecha_ficticia ?? date('Y-m-d');


$filas_por_pagina = 100;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $filas_por_pagina;

$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

$busqueda_fecha = null;
if (!empty($busqueda_fecha)) {
    $fecha_obj = DateTime::createFromFormat('Y-m-d', $busqueda_fecha);
    if ($fecha_obj) {
        $busqueda_fecha = $fecha_obj->format('Y-m-d');
    } else {
        $busqueda_fecha = '';
    }
}

$busqueda_fecha = isset($_GET['fecha_busqueda']) ? trim($_GET['fecha_busqueda']) : '';
if (!empty($busqueda_fecha)) {
    $fecha_obj = DateTime::createFromFormat('Y-m-d', $busqueda_fecha);
    if ($fecha_obj) {
        $busqueda_fecha = $fecha_obj->format('Y-m-d');
    } else {
        $busqueda_fecha = '';
    }
}

$busqueda_avanzada = isset($_GET['busqueda_avanzada']);

$estado_id = isset($_GET['estado']) ? (int)$_GET['estado'] : 1;

$condiciones = ["v.estado_id = :estado_id"];
$parametros = [':estado_id' => $estado_id];

if ($busqueda_avanzada) {
    if (!empty($_GET['origen'])) {
        $condiciones[] = "v.origen LIKE :origen";
        $parametros[':origen'] = '%' . $_GET['origen'] . '%';
    }
    if (!empty($_GET['pais'])) {
        $condiciones[] = "v.pais_destino LIKE :pais_destino";
        $parametros[':pais_destino'] = '%' . $_GET['pais'] . '%';
    }
    if (!empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
        $fecha_inicio = DateTime::createFromFormat('Y-m-d', $_GET['fecha_inicio']);
        $fecha_fin = DateTime::createFromFormat('Y-m-d', $_GET['fecha_fin']);

        if ($fecha_inicio && $fecha_fin) { // Solo si son fechas válidas
            $condiciones[] = "v.fecha BETWEEN :fecha_inicio AND :fecha_fin";
            $parametros[':fecha_inicio'] = $fecha_inicio->format('Y-m-d');
            $parametros[':fecha_fin'] = $fecha_fin->format('Y-m-d');
        }
    }
}

if (!empty($busqueda)) {
    $condiciones[] = "(v.num_vuelo LIKE :busqueda
                    OR v.pais_destino LIKE :busqueda
                    OR v.ciudad_destino LIKE :busqueda
                    OR v.origen LIKE :busqueda
                    OR ac.isla LIKE :busqueda
                    OR v.codigo LIKE :busqueda)";
                    $parametros[':busqueda'] = '%' . $busqueda . '%';
}

if (!empty($busqueda_fecha)) {
    $condiciones[] = "v.fecha = :busqueda_fecha";
    $parametros[':busqueda_fecha'] = $busqueda_fecha;
}

$sql = "SELECT v.id, v.num_vuelo, v.pais_destino AS destino, v.fecha, v.hora_salida, v.origen, v.estado_id, v.encuestas_realizadas, ac.isla AS aeropuerto_nombre
        FROM vuelos v
        LEFT JOIN aeropuertos_canarias ac ON v.origen = ac.codigo_iata
        WHERE " . implode(" AND ", $condiciones) . "
        ORDER BY v.fecha DESC, v.hora_salida ASC
        LIMIT :limite OFFSET :offset";

$stmt = $conexion->prepare($sql);

foreach ($parametros as $clave => $valor) {
    $stmt->bindValue($clave, $valor, PDO::PARAM_STR);
}

$stmt->bindValue(':limite', (int)$filas_por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

$stmt->execute();
$vuelos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql_count = "SELECT COUNT(*) as total FROM vuelos v
              LEFT JOIN aeropuertos_canarias ac ON v.origen = ac.codigo_iata
              WHERE " . implode(" AND ", $condiciones);

$stmt_count = $conexion->prepare($sql_count);

foreach ($parametros as $clave => $valor) {
    $stmt_count->bindValue($clave, $valor, PDO::PARAM_STR);
}

$stmt_count->execute();
$resultado = $stmt_count->fetch(PDO::FETCH_ASSOC);
$total_vuelos = isset($resultado['total']) ? (int)$resultado['total'] : 0;

$total_paginas = ($total_vuelos > 0) ? ceil($total_vuelos / $filas_por_pagina) : 1;
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestión de Vuelos</title>
    <!-- <link rel="stylesheet" href="../css/styles.css" /> REACTIVAR ESTA Y ELIMINAR LA OTRA EN LA VERSIÓN FINAL-->
    <link rel="stylesheet" href="../css/variables.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>">

    <script>
      // Función para mostrar/ocultar la búsqueda avanzada
      function toggleBusquedaAvanzada() {
        const advancedSearch = document.getElementById("busqueda-avanzada-modal");
        advancedSearch.style.display = advancedSearch.style.display === "block" ? "none" : "block";
      }

      function closeModal() {
        const advancedSearch = document.getElementById("busqueda-avanzada-modal");
        advancedSearch.style.display = "none";
      }
    </script>
  </head>
  <body>

    <header class="nav-superior">

        <!-- Menú Superior Fixed -->
        <nav class="menu-superior" role="navigation" aria-label="Menú principal">
            <ul>
                <li><a href="listado.php" title="Inicio" id="logo-menu-superior">
                    <img src="../assets/icons/SE_2_colores-removebg-preview.png" alt="Logo">
                </a></li>

                <li><a href="info.php" title="Información">
                    <img src="../assets/icons/info.png" alt="Información">
                </a></li>

                <li>
                    <a href="javascript:void(0);" title="Actualizar estado de vuelos según la fecha actual" onclick="actualizarEstadosVuelos()">
                        <img src="../assets/icons/icon-refresh.png" alt="Actualizar estados de vuelos">
                    </a>
                </li>

                <li><a href="cargar_xls.php" title="Cargar .xls">
                    <img src="../assets/icons/upload.png" alt="Cargar XLS">
                </a></li>

                <li><a href="exportar_y_enviar_xls.php" title="Enviar reporte .xls de todos los datos a la empresa">
                    <img src="../assets/icons/enviar-datos-xls.png" alt="Enviar XLS">
                </a></li>

                <li><a href="agregar_vuelo.php" title="Añadir vuelo manualmente">
                    <img src="../assets/icons/add.png" alt="Añadir vuelo">
                </a></li>

                <li><a href="eliminar_datos.php" title="Eliminar datos de la base de datos">
                    <img src="../assets/icons/delete.png" alt="Eliminar datos">
                </a></li>

                <li class="separado"><a href="perfil.php" title="Perfil de usuario">
                    <img src="../assets/icons/icon-user.png" alt="Perfil de usuario">
                </a></li>

                <li class="separado"><a href="../public/logout.php" title="Cerrar sesión">
                    <img src="../assets/icons/logout.png" alt="Cerrar sesión">
                </a></li>
            </ul>
        </nav>

        <!-- Contenedor de búsqueda y bienvenida -->
        <div class="search-and-welcome">
            <form id="form-busqueda" action="listado.php" method="GET" role="search" aria-label="Formulario de búsqueda de vuelos">
                <input type="text" name="busqueda" id="busqueda"
                    placeholder="Buscar vuelo..."
                    title="Puedes buscar por:
                    - N° de vuelo (ej: IB1234)
                    - País de Destino (ej: España, Francia)
                    - Ciudad de Destino (ej: París, Berlín)
                    - Código IATA (ej: MAD, ORK, TFN, LPG)
                    - Nombre de aeropuerto (ej: Lanzarote, Tenerife Sur)
                    autocomplete="off"
                    value="<?php echo htmlspecialchars($busqueda); ?>">

                <input type="date" name="fecha_busqueda" id="fecha_busqueda"
                    value="<?php echo htmlspecialchars($busqueda_fecha); ?>"
                    title="Filtrar por fecha específica">

                <input type="hidden" name="estado" value="<?php echo htmlspecialchars($estado_id); ?>">

                <button id="boton-buscar" type="submit" aria-label="Buscar vuelo">
                    <img src="../assets/icons/buscar.png" alt="Buscar">
                </button>
            </form>
        </div>


        <!-- Botón del menú hamburguesa con recuadro -->
        <button id="menu-toggle" aria-label="Abrir/Cerrar menú">
            <div class="menu-toggle-box">
                <img src="../assets/icons/icon-menu-ham.png" alt="Menú">
            </div>
        </button>

        <!-- Menú Hamburguesa -->
        <nav id="menu-hamburguesa" class="menu-hamburguesa">
            <button id="menu-close" aria-label="Cerrar menú">&times;</button>
            <ul>
                <li><a href="listado.php"><img src="../assets/icons/SE_2_colores-removebg-preview.png" alt="Inicio">Inicio</a></li>
                <li><a href="info.php"><img src="../assets/icons/info.png" alt="Información">Información</a></li>
                <li>
                    <a href="javascript:void(0);" onclick="actualizarEstadosVuelos()">
                        <img src="../assets/icons/icon-refresh.png" alt="Actualizar vuelos">
                        Actualizar vuelos disponibles
                    </a>
                </li>
                <li><a href="cargar_xls.php"><img src="../assets/icons/upload.png" alt="Cargar XLS">Cargar XLS</a></li>
                <li><a href="exportar_y_enviar_xls.php"><img src="../assets/icons/enviar-datos-xls.png" alt="Enviar XLS">Enviar XLS</a></li>
                <li><a href="agregar_vuelo.php"><img src="../assets/icons/add.png" alt="Añadir Vuelo">Añadir Vuelo</a></li>
                <li><a href="eliminar_datos.php"><img src="../assets/icons/delete.png" alt="Eliminar Datos">Eliminar Datos</a></li>
                <li><a href="perfil.php"><img src="../assets/icons/icon-user.png" alt="Perfil">Perfil</a></li>
                <li><a href="../public/logout.php"><img src="../assets/icons/logout.png" alt="Cerrar Sesión">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <!-- Mensaje de notificación de exito o fracaso en la ejecución de una acción -->
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

    <h1 class="ocultar" >Gestión de Vuelos</h1>

    <div class="bienvenida-usuario">
        Bienvenido,&nbsp;<strong><?php echo htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Invitado'); ?></strong>
    </div>

    <p><strong>Mostrando:</strong>
        <?php
        $vuelos_mostrados = min($filas_por_pagina, $total_vuelos - $offset);
        echo ($offset + 1) . ' - ' . ($offset + $vuelos_mostrados) . ' de ' . number_format($total_vuelos);
        ?>
    </p>

    <div id="busqueda-avanzada-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2>Búsqueda Avanzada</h2>
            <form action="listado.php" method="GET">
                <input type="text" name="origen" placeholder="Buscar por Origen" value="<?php echo htmlspecialchars($_GET['origen'] ?? ''); ?>">
                <input type="text" name="pais" placeholder="Buscar por País de Destino" value="<?php echo htmlspecialchars($_GET['pais'] ?? ''); ?>">
                <input type="date" name="fecha_inicio" placeholder="Fecha Inicio" value="<?php echo htmlspecialchars($_GET['fecha_inicio'] ?? ''); ?>">
                <input type="date" name="fecha_fin" placeholder="Fecha Fin" value="<?php echo htmlspecialchars($_GET['fecha_fin'] ?? ''); ?>">
                <button type="submit">Buscar</button>
                <input type="hidden" name="busqueda_avanzada" value="true">
            </form>
        </div>
    </div>

    <section id="contenedor-principal-de-tabla">

    <div class="filtros-estado">
        <button class="boton-expirado" onclick="filtrarEstado(3)">Expirado</button>
        <button class="boton-activo" onclick="filtrarEstado(1)">Activo</button>
        <button class="boton-encuestado" onclick="filtrarEstado(2)">Encuestado</button>
    </div>

    <table>
        <thead class="ocultar">
            <tr>
                <th>Número de Vuelo</th>
                <th>Destino</th>
                <th>Fecha y Hora</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($vuelos as $vuelo): ?>
                <?php
                $estadoId = isset($vuelo['estado_id']) ? (int)$vuelo['estado_id'] : null;
                $claseEstado = '';

                switch ($estadoId) {
                    case 1:
                        $claseEstado = 'vuelo-activo';
                        break;
                    case 2:
                        $claseEstado = 'vuelo-encuestado';
                        break;
                    case 3:
                        $claseEstado = 'vuelo-expirado';
                        break;
                    default:
                        $claseEstado = 'vuelo-desconocido';
                        break;
                }
                ?>

                <tr class="fila-clickable <?php echo $claseEstado; ?>"
                data-id="<?php echo $vuelo['id']; ?>"
                data-estado="<?php echo $vuelo['estado_id']; ?>"
                >

                    <td class="numero-vuelo"><?php echo htmlspecialchars($vuelo['num_vuelo']); ?></td>

                    <td class="destino"><?php echo htmlspecialchars($vuelo['destino']); ?></td>

                    <td class="contenedor-fecha-hora">
                        <div class="contenido-fecha-hora">
                            <span class="fecha"><?php echo htmlspecialchars($vuelo['fecha']); ?></span><br>
                            <span><?php echo htmlspecialchars($vuelo['hora_salida']); ?></span>
                        </div>
                        <?php if ($vuelo['fecha'] === $fecha_actual): ?>
                            <img src="../assets/icons/vuelo-hoy.png" alt="Vuelo de hoy" title="Este vuelo es hoy" class="icono-vuelo-hoy">
                        <?php endif; ?>
                    </td>

                    <td onclick="event.stopPropagation();">
                        <input type="number"
                            class="input-encuestas"
                            data-id="<?php echo $vuelo['id']; ?>"
                            value="<?php echo trim($vuelo['encuestas_realizadas']); ?>"
                            min="0"
                            max="850">
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="paginacion">
        <?php
        $query_params = http_build_query([
            'estado' => $estado_id,
            'busqueda' => $busqueda,
            'busqueda_avanzada' => $busqueda_avanzada,
            'origen' => $_GET['origen'] ?? '',
            'pais' => $_GET['pais'] ?? '',
            'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
            'fecha_fin' => $_GET['fecha_fin'] ?? ''
        ]);
        ?>

        <?php if ($pagina_actual > 1): ?>
            <a href="?pagina=<?php echo $pagina_actual - 1; ?>&<?php echo $query_params; ?>" class="boton-paginacion">&laquo; Anterior</a>
        <?php endif; ?>

        <span class="contador-paginacion">Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?></span>

        <?php if ($pagina_actual < $total_paginas): ?>
            <a href="?pagina=<?php echo $pagina_actual + 1; ?>&<?php echo $query_params; ?>" class="boton-paginacion">Siguiente &raquo;</a>
        <?php endif; ?>
    </div>
    </section>

    <script>
        const NOTIFICACION_TIEMPO = <?php echo $_ENV['NOTIFICACION_TIEMPO'] ?? 8; ?>;
    </script>

    <script src="../js/scripts.js"></script>
    <script src="../js/cuenta_atras.js"></script>
    <script src="../js/notificaciones.js"></script>
    <script src="../js/menu-hamburguesa.js"></script>
    <script src="../js/swiper.js"></script>
    <script src="../js/acceso_doble_click_detalle.js"></script>

  </body>
</html>