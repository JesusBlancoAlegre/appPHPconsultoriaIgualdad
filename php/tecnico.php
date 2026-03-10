<?php
declare(strict_types=1);

// DEBUG 
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/auth.php';
require_role('TECNICO');

require __DIR__ . '/config.php';

function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function formatBytes(int $bytes): string {
  $u = ['B','KB','MB','GB','TB']; $i=0; $v=(float)$bytes;
  while ($v >= 1024 && $i < count($u)-1) { $v/=1024; $i++; }
  return round($v, 2) . ' ' . $u[$i];
}

$techUsername = (string)($_SESSION['user']['username'] ?? 'tecnico');

// Clientes para el selector
$clientes = [];
$res = db()->query("
  SELECT u.id, u.username
  FROM usuario u
  JOIN rol r ON r.id = u.rol_id
  WHERE r.nombre = 'CLIENTE'
  ORDER BY u.username
");
while ($row = $res->fetch_assoc()) $clientes[] = $row;

//  Cliente seleccionado
$clienteId = isset($_GET['cliente_id']) ? (int)$_GET['cliente_id'] : 0;
if ($clienteId === 0 && !empty($clientes)) {
  $clienteId = (int)$clientes[0]['id'];
}

// Avisos del cliente (últimos 50)
$avisos = [];
if ($clienteId > 0) {
  $stmt = db()->prepare("
    SELECT tipo, mensaje, creado_en
    FROM aviso
    WHERE usuario_id = ?
    ORDER BY creado_en DESC
    LIMIT 50
  ");
  $stmt->bind_param('i', $clienteId);
  $stmt->execute();
  $r = $stmt->get_result();
  while ($row = $r->fetch_assoc()) $avisos[] = $row;
}

//  Archivos del cliente (últimos 200)
$archivos = [];
if ($clienteId > 0) {
  $stmt = db()->prepare("
    SELECT id, nombre_original, tamano_bytes, subido_en
    FROM archivo
    WHERE usuario_id = ?
    ORDER BY subido_en DESC
    LIMIT 200
  ");
  $stmt->bind_param('i', $clienteId);
  $stmt->execute();
  $r = $stmt->get_result();
  while ($row = $r->fetch_assoc()) $archivos[] = $row;
}

require __DIR__ . '/../html/tecnico.html.php';