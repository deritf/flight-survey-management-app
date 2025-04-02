<?php
require_once '../config/conexion.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

session_start();

date_default_timezone_set('Atlantic/Canary');

set_time_limit(120); // Aumenta a 2 minutos, puedes ajustarlo si hace falta
ini_set('memory_limit', '512M'); // suficiente RAM
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');
error_reporting(E_ALL);

function mantenerSoloUltimosArchivos($dir, $maxArchivos = 3) {
    if (!is_dir($dir)) return;

    // Obtener todos los archivos XLS/XLSX ordenados por fecha de modificación (más recientes primero)
    $archivos = glob($dir . '/*.xls');
    $archivos = array_merge($archivos, glob($dir . '/*.xlsx'));

    // Ordenar por fecha de modificación (de más reciente a más antiguo)
    usort($archivos, function ($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    // Si hay más archivos de los permitidos, elimina los más antiguos
    if (count($archivos) > $maxArchivos) {
        $archivosAEliminar = array_slice($archivos, $maxArchivos);
        foreach ($archivosAEliminar as $archivo) {
            unlink($archivo);
            error_log("Archivo eliminado (por límite de historial): $archivo");
        }
    }
}

$dotenv = Dotenv::createImmutable(__DIR__ . '/../config');
$dotenv->load();

$SMTP_HOST = $_ENV['SMTP_HOST'];
$SMTP_PORT = $_ENV['SMTP_PORT'];
$SMTP_USER = $_ENV['SMTP_USER'];
$SMTP_PASS = $_ENV['SMTP_PASS'];
$MAIL_FROM_NAME = $_ENV['MAIL_FROM_NAME'];
$MAIL_TO = $_ENV['MAIL_TO'];
$CONTACT_EMAIL = $_ENV['CONTACT_EMAIL'];
$CONTACT_PHONE = $_ENV['CONTACT_PHONE'];

$usuario_id = $_SESSION['usuario_id'] ?? null;
$nombre_usuario = "desconocido";
$apellido_usuario = "xxx";

if ($usuario_id) {
    $stmt = $conexion->prepare("SELECT nombre, apellido1, apellido2 FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $nombre_usuario = strtolower($usuario['nombre']);
        $apellido_usuario = strtolower(substr($usuario['apellido1'], 0, 3));

        $nombre_completo = ucwords(strtolower(trim("{$usuario['nombre']} {$usuario['apellido1']} {$usuario['apellido2']}")));
    }
}

try {
    $sql = "SELECT v.obs, v.hora_salida, v.origen, v.dia_semana, v.codigo, v.ciudad_destino,
                   v.pais_destino, v.escala, v.aeronave, v.num_vuelo, v.opera_desde, v.opera_hasta,
                   v.fecha, v.encuestas_realizadas, v.estado_id
            FROM vuelos v";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $vuelos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$vuelos) {
        header("Location: listado.php?status=error&message=No hay datos para exportar.");
        exit();
    }

    $total_activos = count(array_filter($vuelos, fn($vuelo) => $vuelo['estado_id'] == 1));
    $total_encuestados = count(array_filter($vuelos, fn($vuelo) => $vuelo['estado_id'] == 2));
    $total_expirados = count(array_filter($vuelos, fn($vuelo) => $vuelo['estado_id'] == 3));

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = [
        'OBS', 'Hora de Salida', 'Origen', 'Día Semana', 'Código',
        'Ciudad Destino', 'País Destino', 'Escala', 'Aeronave', 'N° Vuelo',
        'Opera Desde', 'Opera Hasta', 'Fecha', 'Encuestas Realizadas', 'Estado'
    ];

    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '1', strtoupper($header));
        $sheet->getStyle($col . '1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => 'center']
        ]);
        $col++;
    }

    $row = 2;
    foreach ($vuelos as $vuelo) {
        $col = 'A';
        $colIndex = 0;
        foreach ($vuelo as $key => $value) {
            $cell = $col . $row;
            $sheet->setCellValue($cell, $value);

            if ($key === 'encuestas_realizadas' && !is_null($value) && (int)$value !== 0) {
                $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00'); // Amarillo
            }

            $col++;
            $colIndex++;
        }

        $fillColor = match($vuelo['estado_id']) {
            1 => 'FFFFFF', // Activo
            2 => '5cb85c', // Encuestado
            3 => 'd9534f', // Expirado
            default => 'FFFFFF',
        };

        $columns = range('A', 'O');
        foreach ($columns as $column) {
            if ($column !== 'N' || (isset($vuelo['encuestas_realizadas']) && ((int)$vuelo['encuestas_realizadas'] === 0 || is_null($vuelo['encuestas_realizadas'])))) {
                $sheet->getStyle("{$column}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($fillColor);
            }
        }

        $row++;
    }

    foreach (range('A', 'O') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $fecha_actual = date('d-m-Y_H-i-s');
    $fileName = "vuelos_exp_{$nombre_usuario}_{$apellido_usuario}_{$fecha_actual}.xls";
    $filePath = "../exports/" . $fileName;

    $writer = new Xls($spreadsheet);
    $writer->save($filePath);

    mantenerSoloUltimosArchivos('../exports', 3);

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = $SMTP_USER;
    $mail->Password = $SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $SMTP_PORT;

    $mail->setFrom($SMTP_USER, $MAIL_FROM_NAME);
    $mail->addAddress($MAIL_TO);
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = "Exportación de datos - {$nombre_usuario} {$apellido_usuario} - {$fecha_actual}";

    $mail->Body = "
        <p>Hola,</p>

        <p>El usuario <strong>{$nombre_completo}</strong> ha realizado una <strong>exportación de datos</strong> desde el sistema.</p>

        <h3>Detalles de la Exportación</h3>
        <ul>
            <li><strong>Fecha y hora de exportación:</strong> " . date('d-m-Y H:i:s') . "</li>
            <li><strong>Archivo generado:</strong> {$fileName}</li>
            <li><strong>Registros totales:</strong> " . count($vuelos) . "</li>
            <li><span style='color:#5cb85c;'><strong>Encuestados:</strong> {$total_encuestados}</span></li>
            <li><span style='color:#d9534f;'><strong>Expirados:</strong> {$total_expirados}</span></li>
            <li><span style='color:#000000;'><strong>Activos:</strong> {$total_activos}</span></li>
        </ul>

        <p>El archivo ha sido enviado correctamente al correo: <strong>{$MAIL_TO}</strong>.</p>

        <p>Por favor, <strong>no respondas a este correo</strong>. Si necesitas más información, contacta con nosotros en:</p>
        <ul>
            <li><a href='mailto:{$CONTACT_EMAIL}'>{$CONTACT_EMAIL}</a></li>
            <li><a href='tel:{$CONTACT_PHONE}'>{$CONTACT_PHONE}</a></li>
        </ul>

        <p>Saludos,</p>
        <p><strong>Sistema Web Automático</strong></p>
    ";

    $mail->addAttachment($filePath);

    $mail->send();

    header("Location: listado.php?status=success&message=Archivo exportado y enviado correctamente al correo: {$MAIL_TO}");
} catch (Exception $e) {
    error_log("Error al exportar y enviar archivo: " . $e->getMessage(), 3, "../logs/error.log");
    header("Location: listado.php?status=error&message=Error al exportar y enviar el archivo al correo: {$MAIL_TO}");
}

exit();