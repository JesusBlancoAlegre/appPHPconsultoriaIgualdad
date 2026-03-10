<?php
// Controlador para manejar el login, verificando credenciales y redirigiendo según rol
declare(strict_types=1);

session_start();
require __DIR__ . '/config.php';

function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$error = '';
// Si ya está logueado, redirigir según rol
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = (string)($_POST['password'] ?? '');

  if ($username === '' || $password === '') {
    $error = 'Rellena usuario y contraseña.';
  } else {
    $sql = "
      SELECT u.id, u.username, u.password, r.nombre AS rol
      FROM usuario u
      LEFT JOIN rol r ON r.id = u.rol_id
      WHERE u.username = ?
      LIMIT 1
    ";
    $stmt = db()->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    // Verificar contraseña
    if (!$user || !password_verify($password, (string)$user['password'])) {
      $error = 'Usuario o contraseña incorrectos.';
    } else {
      session_regenerate_id(true);

      $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'username' => (string)$user['username'],
        'rol' => (string)($user['rol'] ?? ''),
      ];

      // redirección por rol
      $rol = $_SESSION['user']['rol'];
      if ($rol === 'ADMINISTRADOR') { header('Location: admin.php'); exit; }
      if ($rol === 'TECNICO')       { header('Location: tecnico.php'); exit; }
      if ($rol === 'CLIENTE')       { header('Location: cliente.php'); exit; }

      header('Location: panel.php'); exit;
    }
  }
}

require __DIR__ . '/../html/login.html.php';