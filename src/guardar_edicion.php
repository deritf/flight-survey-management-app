<?php
require_once '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];

    $num_vuelo = strtoupper(trim($_POST["num_vuelo"]));
    $aeronave = strtoupper(trim($_POST["aeronave"]));
    $codigo = strtoupper(trim($_POST["codigo"]));
    $origen = strtoupper(trim($_POST["origen"]));
    $escala = strtoupper(trim($_POST["escala"])) ?: null;
    $pais_destino = strtoupper(trim($_POST["destino"]));
    $ciudad_destino = strtoupper(trim($_POST["ciudad"]));

    $dia_semana = strtoupper(str_replace(' ', '', $_POST["dia_semana"]));

    $opera_desde = $_POST["opera_desde"];
    $opera_hasta = $_POST["opera_hasta"];
    $fecha = $_POST["fecha"];
    $hora_salida = $_POST["hora_salida"];
    $estado_id = $_POST["estado_id"];

    $filtros = ['busqueda', 'fecha_busqueda', 'estado', 'busqueda_avanzada', 'origen', 'pais', 'fecha_inicio', 'fecha_fin'];
    $filtros_preservados = [];
    foreach ($filtros as $filtro) {
        $campo = "filtro_" . $filtro;
        if (isset($_POST[$campo]) && $_POST[$campo] !== '') {
            $filtros_preservados[$filtro] = $_POST[$campo];
        }
    }

    try {
        $sql = "UPDATE vuelos
                SET num_vuelo = :num_vuelo, aeronave = :aeronave, codigo = :codigo, origen = :origen,
                    escala = :escala, pais_destino = :pais_destino, ciudad_destino = :ciudad_destino, dia_semana = :dia_semana,
                    opera_desde = :opera_desde, opera_hasta = :opera_hasta,
                    fecha = :fecha, hora_salida = :hora_salida, estado_id = :estado_id
                WHERE id = :id";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':num_vuelo' => $num_vuelo,
            ':aeronave' => $aeronave,
            ':codigo' => $codigo,
            ':origen' => $origen,
            ':escala' => $escala,
            ':pais_destino' => $pais_destino,
            ':ciudad_destino' => $ciudad_destino,
            ':dia_semana' => $dia_semana,
            ':opera_desde' => $opera_desde,
            ':opera_hasta' => $opera_hasta,
            ':fecha' => $fecha,
            ':hora_salida' => $hora_salida,
            ':estado_id' => $estado_id
        ]);

        $query = http_build_query(array_merge(
            ['id' => $id, 'status' => 'success', 'message' => 'Vuelo actualizado correctamente.'],
            $filtros_preservados
        ));

        header("Location: detalle.php?$query");
        exit();

    } catch (PDOException $e) {
        $query = http_build_query(array_merge(
            ['id' => $id, 'status' => 'error', 'message' => 'Error al actualizar el vuelo: ' . $e->getMessage()],
            $filtros_preservados
        ));
        header("Location: detalle.php?$query");
        exit();
    }
} else {
    header("Location: listado.php");
    exit();
}