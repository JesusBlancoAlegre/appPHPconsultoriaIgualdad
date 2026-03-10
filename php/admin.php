<?php
declare(strict_types=1);

// SEGURIDAD / AUTORIZACIÓN
// auth.php: normalmente inicia sesión, rellena $_SESSION['user'] y funciones de auth
require __DIR__ . '/auth.php';

// require_role: bloquea si el usuario no tiene el rol requerido
require_role('ADMINISTRADOR');

// Username del admin logueado 
$adminUsername = (string)($_SESSION['user']['username'] ?? 'admin');

// CONFIGURACIÓN / BD
// config.php: suele contener db() para obtener el objeto mysqli y config general
require __DIR__ . '/config.php';

/** imprimir datos del usuario/BD de forma segura. */
function h($s): string {
  return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// SISTEMA DE VISTAS POR GET
$allowedViews = ['menu', 'add', 'edit', 'delete'];

// view viene de la URL: admin.php?view=edit (si no viene -> menu)
$view = (string)($_GET['view'] ?? 'menu');

// Si el valor no está permitido, volvemos a menu por seguridad
if (!in_array($view, $allowedViews, true)) {
  $view = 'menu';
}

// CARGA DE DATOS: USUARIOS

$usuarios = [];

$res = db()->query("
  SELECT u.id, u.username, u.rol_id, r.nombre AS rol
  FROM usuario u
  LEFT JOIN rol r ON r.id = u.rol_id
  ORDER BY u.id DESC
");

// Pasamos el resultado a un array PHP ($usuarios)
while ($row = $res->fetch_assoc()) {
  $usuarios[] = $row;
}


// CARGA DE DATOS: ROLES
$roles = [];

$res = db()->query("SELECT id, nombre FROM rol ORDER BY id");

// Pasamos el resultado a un array PHP ($roles)
while ($row = $res->fetch_assoc()) {
  $roles[] = $row;
}

require __DIR__ . '/../html/admin.html.php';