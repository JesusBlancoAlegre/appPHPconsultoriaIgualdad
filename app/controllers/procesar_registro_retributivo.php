<?php
session_start();

require '../vendor/autoload.php';
require '../config/config.php';  // ✅ Incluir config
require '../models/ArchivoModel.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Validaciones
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 3) {
    http_response_code(403);
    die(json_encode(['error' => 'No autorizado']));
}

if (!isset($_FILES['excel']) || $_FILES['excel']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    die(json_encode(['error' => 'Error en la carga del archivo']));
}

try {
    $archivoTmp = $_FILES['excel']['tmp_name'];
    $nombreOriginal = $_FILES['excel']['name'];
    
    // Validar extensión
    $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
    if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
        throw new Exception("Solo se permiten archivos Excel");
    }

    // Cargar y convertir a XLSX
    $spreadsheet = IOFactory::load($archivoTmp);
    $nuevoNombre = pathinfo($nombreOriginal, PATHINFO_FILENAME) . "_" . time() . ".xlsx";
    $rutaSalida = UPLOADS_DIR . $nuevoNombre;

    if (!is_dir(UPLOADS_DIR)) {
        mkdir(UPLOADS_DIR, 0755, true);
    }

    $writer = new Xlsx($spreadsheet);
    $writer->save($rutaSalida);

    // ✅ GUARDAR EN BD
    $archivoModel = new ArchivoModel($pdo);
    $idArchivo = $archivoModel->guardar([
        'tipo' => 'REGISTRO_RETRIBUTIVO',
        'nombre_original' => $nombreOriginal,
        'nombre_guardado' => $nuevoNombre,
        'ruta_relativa' => $rutaSalida,
        'tamano_bytes' => filesize($rutaSalida),
        'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'sha256' => hash_file('sha256', $rutaSalida),
        'id_cliente_medida' => $_SESSION['id_cliente_medida'] ?? 1
    ]);

    // Obtener datos
    $sheet = $spreadsheet->getActiveSheet();
    $datos = $sheet->toArray();

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'id_archivo' => $idArchivo,
        'archivo' => $nuevoNombre,
        'tamano' => filesize($rutaSalida),
        'filas' => count($datos)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>