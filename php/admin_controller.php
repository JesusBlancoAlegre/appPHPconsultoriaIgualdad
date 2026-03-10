<?php
declare(strict_types=1);
// SEGURIDAD / AUTORIZACIÓN
// auth.php inicia la sesión 
// require_role('ADMINISTRADOR'  corta la ejecución si el usuario no es administrador.

require __DIR__ . '/auth.php';
require_role('ADMINISTRADOR');
require __DIR__ . '/config.php';

/** Redirige al menú principal del admin (admin.php)*/
function redirect_menu(string $msg = ''): void {
  $to = 'admin.php';
  if ($msg !== '') $to .= '?msg=' . urlencode($msg); 
  header("Location: $to");
  exit;
}

/** Redirige a una vista concreta del admin */
function redirect_view(string $view, string $msg = ''): void {
  $to = 'admin.php?view=' . urlencode($view);
  if ($msg !== '') $to .= '&msg=' . urlencode($msg);
  header("Location: $to");
  exit;
}


// VALIDACIÓN DE MÉTODO HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Método no permitido');
}

// LECTURA DE ACCIÓN

$accion = $_POST['accion'] ?? '';

// ACCIÓN: CREAR USUARIO

if ($accion === 'crear') {
  // Leer campos del formulario
  $username = trim($_POST['username'] ?? '');
  $password = (string)($_POST['password'] ?? '');
  $rol_id   = (int)($_POST['rol_id'] ?? 0);

  // Validar que no falten datos
  if ($username === '' || $password === '' || $rol_id <= 0) {
    // Volver a la vista add con un mensaje
    redirect_view('add', 'Faltan datos');
  }

  // Hashear contraseña antes de guardarla en BD 
  $hash = password_hash($password, PASSWORD_DEFAULT);

  // Insertar usuario en BD
  $stmt = db()->prepare("INSERT INTO usuario (username, password, rol_id) VALUES (?, ?, ?)");
  $stmt->bind_param('ssi', $username, $hash, $rol_id);
  $stmt->execute();

  // Volver al menú con mensaje
  redirect_menu('Usuario creado');
}

// ACCIÓN: EDITAR USUARIO

if ($accion === 'editar') {
  // Leer campos del formulario
  $id       = (int)($_POST['id'] ?? 0);
  $username = trim($_POST['username'] ?? '');
  $rol_id   = (int)($_POST['rol_id'] ?? 0);
  $password = (string)($_POST['password'] ?? ''); 

  // Validar datos mínimos
  if ($id <= 0 || $username === '' || $rol_id <= 0) {
    redirect_view('edit', 'Faltan datos');
  }

  // Si el admin escribió contraseña, se cambia; si la deja vacía, NO se actualiza
  if ($password !== '') {
    // Hashear la nueva contraseña
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Update incluyendo password
    $stmt = db()->prepare("UPDATE usuario SET username = ?, rol_id = ?, password = ? WHERE id = ?");
    $stmt->bind_param('sisi', $username, $rol_id, $hash, $id);
    $stmt->execute();
  } else {
    // Update sin tocar password
    $stmt = db()->prepare("UPDATE usuario SET username = ?, rol_id = ? WHERE id = ?");
    $stmt->bind_param('sii', $username, $rol_id, $id);
    $stmt->execute();
  }

  // Redirigir al menú con confirmación
  redirect_menu('Usuario actualizado');
}


// ACCIÓN: ELIMINAR USUARIO
if ($accion === 'eliminar') {
  // ID del usuario a eliminar
  $id = (int)($_POST['id'] ?? 0);
  if ($id <= 0) redirect_view('delete', 'ID inválido');

  // Evitar que el admin se elimine a sí mismo (bloqueo de seguridad)
  $currentId = (int)($_SESSION['user']['id'] ?? 0);
  if ($currentId === $id) redirect_view('delete', 'No puedes eliminar tu propio usuario');

  // Borrar usuario
  $stmt = db()->prepare("DELETE FROM usuario WHERE id = ?");
  $stmt->bind_param('i', $id);
  $stmt->execute();


  redirect_menu('Usuario eliminado');
}

// Si no coincide con crear/editar/eliminar, devolvemos mensaje genérico.
redirect_menu('Acción no válida');