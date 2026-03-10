<?php
declare(strict_types=1);

require __DIR__ . '/auth.php';
require_role('CLIENTE'); 

require __DIR__ . '/config.php';

function redirect_cliente(string $msg): void {
  header('Location: cliente.php?msg=' . urlencode($msg));
  exit;
}

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Método no permitido');
}

// Validar que exista fileToUpload
if (!isset($_FILES['fileToUpload'])) {
  redirect_cliente('No se recibió archivo');
}

$clienteId = (int)($_SESSION['user']['id'] ?? 0);
if ($clienteId <= 0) {
  redirect_cliente('Sesión inválida');
}
$clienteUsername = (String)($_SESSION['user']['username'] ?? 'desconocido');
if (empty($clienteUsername)) {
  redirect_cliente('Sesión inválida');
}


// Crear directorios
$uploadsBase = __DIR__ . '/../uploads';
if (!is_dir($uploadsBase) && !mkdir($uploadsBase, 0775, true)) {
  redirect_cliente('No se pudo crear carpeta uploads');
}

$clientDir = $uploadsBase . '/cliente_' . $clienteUsername;
if (!is_dir($clientDir) && !mkdir($clientDir, 0775, true)) {
  redirect_cliente('No se pudo crear carpeta del cliente');
}

$db = db();
$uploadedCount = 0;
$errorsList = [];

// Obtener datos de $_FILES
$fileInput = $_FILES['fileToUpload'];

// Convertir a array si es un solo archivo
if (!is_array($fileInput['name'])) {
  $fileInput = [
    'name' => [$fileInput['name']],
    'tmp_name' => [$fileInput['tmp_name']],
    'error' => [$fileInput['error']],
    'size' => [$fileInput['size']],
    'type' => [$fileInput['type']]
  ];
}

// Procesar cada archivo
$totalFiles = count($fileInput['name']);

for ($i = 0; $i < $totalFiles; $i++) {
  $filename = trim((string)($fileInput['name'][$i] ?? ''));
  $tmpfile = (string)($fileInput['tmp_name'][$i] ?? '');
  $uploaderr = (int)($fileInput['error'][$i] ?? UPLOAD_ERR_NO_FILE);
  $filesize = (int)($fileInput['size'][$i] ?? 0);

  // Saltar si no hay nombre
  if (empty($filename)) {
    continue;
  }

  // Validar errores de subida
  if ($uploaderr !== UPLOAD_ERR_OK) {
    $errorsList[] = "$filename: Error de subida (código $uploaderr)";
    continue;
  }

  // Validar que sea archivo temporal válido
  if (empty($tmpfile) || !is_uploaded_file($tmpfile)) {
    $errorsList[] = "$filename: Archivo temporal inválido";
    continue;
  }

  // Validar extensión
  $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
  if (!in_array($ext, ['xlsx', 'xls', 'docx', 'doc'], true)) {
    $errorsList[] = "$filename: Extensión no permitida";
    continue;
  }

  // Validar tamaño (máx 10MB)
  if ($filesize > 10485760) {
    $errorsList[] = "$filename: Archivo demasiado grande (máx 10MB)";
    continue;
  }

  // Generar nombre único
  $newname = bin2hex(random_bytes(16)) . '.' . $ext;
  $newpath = $clientDir . '/' . $newname;

  // Mover archivo
  if (!move_uploaded_file($tmpfile, $newpath)) {
    $errorsList[] = "$filename: No se pudo guardar";
    continue;
  }

  // Guardar en BD
  $relpath = 'uploads/cliente_' . $clienteId . '/' . $newname;
  $mime = 'application/octet-stream';
  $sha = hash_file('sha256', $newpath) ?: '';

  $stmt = $db->prepare("
    INSERT INTO archivo 
    (usuario_id, nombre_original, nombre_guardado, ruta_relativa, tamano_bytes, mime, sha256) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
  ");

  if ($stmt) {
    $stmt->bind_param('isssiss', $clienteId, $filename, $newname, $relpath, $filesize, $mime, $sha);
    $stmt->execute();
    $stmt->close();

    // Registrar aviso
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';
    $stmt = $db->prepare("
      INSERT INTO aviso (usuario_id, tipo, mensaje, detalle, ip) 
      VALUES (?, 'upload', 'Archivo subido', ?, ?)
    ");
    if ($stmt) {
      $stmt->bind_param('iss', $clienteId, $filename, $ip);
      $stmt->execute();
      $stmt->close();
    }

    $uploadedCount++;
  } else {
    $errorsList[] = "$filename: Error en base de datos";
  }
}

// Redirigir con mensaje
if ($uploadedCount > 0) {
  $msg = "$uploadedCount archivo(s) subido(s)";
  if (!empty($errorsList)) {
    $msg .= " - Errores: " . implode('; ', $errorsList);
  }
  redirect_cliente($msg);
} else {
  $msg = empty($errorsList) ? 'No se subió ningún archivo' : implode('; ', $errorsList);
  redirect_cliente($msg);
}