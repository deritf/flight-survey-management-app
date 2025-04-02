<?php
require_once '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $num_vuelo = strtoupper(trim($_POST["num_vuelo"]));
    $aeronave = strtoupper(trim($_POST["aeronave"]));
    $codigo = strtoupper(trim($_POST["codigo"]));
    $origen = strtoupper(trim($_POST["origen"]));
    $escala = strtoupper(trim($_POST["escala"])) ?: null;
    $pais_destino = strtoupper(trim($_POST["destino"]));
    $ciudad_destino = strtoupper(trim($_POST["ciudad"]));
    $dia_semana = strtoupper(trim($_POST["dia_semana"]));
    $opera_desde = $_POST["opera_desde"];
    $opera_hasta = $_POST["opera_hasta"];
    $fecha = $_POST["fecha"];
    $hora_salida = $_POST["hora_salida"];
    $estado_id = $_POST["estado_id"];

    try {
        $sql = "INSERT INTO vuelos (
                    num_vuelo, aeronave, codigo, origen, escala, pais_destino,
                    ciudad_destino, dia_semana, opera_desde, opera_hasta, fecha,
                    hora_salida, estado_id, encuestas_realizadas
                ) VALUES (
                    :num_vuelo, :aeronave, :codigo, :origen, :escala, :pais_destino,
                    :ciudad_destino, :dia_semana, :opera_desde, :opera_hasta, :fecha,
                    :hora_salida, :estado_id, 0
                )";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
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

        header("Location: listado.php?status=success&message=Nuevo+vuelo+añadido+correctamente.");
    } catch (PDOException $e) {
        error_log("Error al agregar vuelo: " . $e->getMessage(), 3, "../logs/error.log");
        header("Location: listado.php?status=error&message=Error+al+agregar+el+nuevo+vuelo.");
    }
} else {
    header("Location: listado.php");
    exit;
}