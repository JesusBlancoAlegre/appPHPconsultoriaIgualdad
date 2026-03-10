<?php
declare(strict_types=1);

require __DIR__ . '/auth.php';
require_role('CLIENTE');

require __DIR__ . '/config.php';

function redirect_menu(string $msg = ''): void {
  $to = 'cliente.php';
  if ($msg !== '') $to .= '?msg=' . urlencode($msg);
  header("Location: $to");
  exit;
}
function redirect_view(string $view, string $msg = ''): void {
  $to = 'admin.php?view=' . urlencode($view);
  if ($msg !== '') $to .= '&msg=' . urlencode($msg);
  header("Location: $to");
  exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Método no permitido');
}

$accion = $_POST['accion'] ?? '';

if ($accion === 'editar') {
  $id       = (int)($_POST['id'] ?? 0);
  $username = trim($_POST['username'] ?? '');
  $password = (string)($_POST['password'] ?? ''); 

  if ($id <= 0 || $username === '' || $rol_id <= 0) {
    redirect_view('edit', 'Faltan datos');
  }

  if ($password !== '') {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare("UPDATE usuario SET username = ?, rol_id = ?, password = ? WHERE id = ?");
    $stmt->bind_param('sisi', $username, $rol_id, $hash, $id);
    $stmt->execute();
  } else {
    $stmt = db()->prepare("UPDATE usuario SET username = ?, rol_id = ? WHERE id = ?");
    $stmt->bind_param('sii', $username, $rol_id, $id);
    $stmt->execute();
  }

  // CLAVE: al guardar vuelve al menú
  redirect_menu('Usuario actualizado');
}
