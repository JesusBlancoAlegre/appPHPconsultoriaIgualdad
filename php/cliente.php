<?php
declare(strict_types=1);

// SEGURIDAD / AUTORIZACIÓN
// auth.php: normalmente inicia sesión, rellena $_SESSION['user'] y funciones de auth
require __DIR__ . '/auth.php';
require_role('CLIENTE');
// CONFIGURACIÓN / BD
// config.php: suele contener db() para obtener el objeto mysqli y config general
require __DIR__ . '/config.php';

function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
// Username del cliente logueado 
$clienteId = (int)($_SESSION['user']['id'] ?? 0);
$clienteUsername = (string)($_SESSION['user']['username'] ?? 'cliente');

$view = $_GET['view'] ?? 'menu';
$allowed = ['menu', 'perfil', 'upload', 'archivos'];
if (!in_array($view, $allowed, true)) $view = 'menu';

// Carga los archivos del cliente 
$archivos = [];
if ($view === 'archivos' && $clienteId > 0) {
  $stmt = db()->prepare("
    SELECT id, nombre_original, subido_en
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

require __DIR__ . '/../html/cliente.html.php';