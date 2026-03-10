<?php
declare(str_types=1);

session_start();
require __DIR__ . '/config.php';

function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$error = '';
$success = '';
$step = $_GET['step'] ?? 'request';

// Si ya está logueado, redirige
if (isset($_SESSION['user'])) {
  $rol = $_SESSION['user']['rol'];
  if ($rol === 'ADMINISTRADOR') { header('Location: admin.php'); exit; }
  if ($rol === 'TECNICO')       { header('Location: tecnico.php'); exit; }
  if ($rol === 'CLIENTE')       { header('Location: cliente.php'); exit; }
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
  $action = $_POST['action'] ?? '';

  // PASO 1: Solicitar recuperación
  if ($action === 'request') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($username === '' || $email === '') {
      $error = 'Usuario y email son obligatorios.';
    } else {
      // Verificar que usuario y email coincidan
      $stmt = db()->prepare("SELECT id FROM usuario WHERE username = ? AND email = ? LIMIT 1");
      $stmt->bind_param('ss', $username, $email);
      $stmt->execute();
      $user = $stmt->get_result()->fetch_assoc();

      if (!$user) {
        $error = 'Usuario o email no coinciden.';
      } else {
        // Generar token único
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = db()->prepare("
          UPDATE usuario 
          SET reset_token = ?, reset_expires = ?
          WHERE id = ?
        ");
        $stmt->bind_param('ssi', $token, $expires, $user['id']);
        $stmt->execute();

        // NOTA: En producción, enviar email con el token
        // Por ahora, mostrar el token en la pantalla (solo para desarrollo)
        $success = "Link de recuperación generado. Token: $token";
        $step = 'reset';
      }
    }
  }
  // PASO 2: Cambiar contraseña
  elseif ($action === 'reset') {
    $token = trim($_POST['token'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $password_confirm = (string)($_POST['password_confirm'] ?? '');

    if ($token === '' || $password === '' || $password_confirm === '') {
      $error = 'Todos los campos son obligatorios.';
    } elseif (strlen($password) < 6) {
      $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($password !== $password_confirm) {
      $error = 'Las contraseñas no coinciden.';
    } else {
      // Verificar token válido y no expirado
      $stmt = db()->prepare("
        SELECT id FROM usuario 
        WHERE reset_token = ? AND reset_expires > NOW()
        LIMIT 1
      ");
      $stmt->bind_param('s', $token);
      $stmt->execute();
      $user = $stmt->get_result()->fetch_assoc();

      if (!$user) {
        $error = 'Token inválido o expirado.';
      } else {
        // Hashear nueva contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Actualizar contraseña y limpiar token
        $stmt = db()->prepare("
          UPDATE usuario 
          SET password = ?, reset_token = NULL, reset_expires = NULL
          WHERE id = ?
        ");
        $stmt->bind_param('si', $hash, $user['id']);
        $stmt->execute();

        $success = 'Contraseña actualizada. Inicia sesión con tu nueva contraseña.';
        $step = 'success';
      }
    }
  }
}

require __DIR__ . '/../html/forgot_password.html.php';