<?php
declare(strict_types=1);

session_start();
require __DIR__ . '/config.php';

function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$error = '';
$success = '';

// Si ya está logueado, redirige al panel correspondiente
if (isset($_SESSION['user'])) {
  $rol = $_SESSION['user']['rol'];
  if ($rol === 'ADMINISTRADOR') { header('Location: admin.php'); exit; }
  if ($rol === 'TECNICO')       { header('Location: tecnico.php'); exit; }
  if ($rol === 'CLIENTE')       { header('Location: cliente.php'); exit; }
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = (string)($_POST['password'] ?? '');
  $password_confirm = (string)($_POST['password_confirm'] ?? '');
  $email = trim($_POST['email'] ?? '');

  // Validaciones
  if ($username === '' || $password === '' || $email === '') {
    $error = 'Todos los campos son obligatorios.';
  } elseif (strlen($username) < 3) {
    $error = 'El usuario debe tener al menos 3 caracteres.';
  } elseif (strlen($password) < 6) {
    $error = 'La contraseña debe tener al menos 6 caracteres.';
  } elseif ($password !== $password_confirm) {
    $error = 'Las contraseñas no coinciden.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Email inválido.';
  } else {
    // Verificar si usuario ya existe
    $stmt = db()->prepare("SELECT id FROM usuario WHERE username = ? LIMIT 1");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
      $error = 'El usuario ya existe.';
    } else {
      // Obtener ID del rol CLIENTE
      $res = db()->query("SELECT id FROM rol WHERE nombre = 'CLIENTE' LIMIT 1");
      $rolRow = $res->fetch_assoc();
      $rolId = $rolRow ? (int)$rolRow['id'] : 0;

      if ($rolId <= 0) {
        $error = 'Error: no se puede asignar rol. Contacte al administrador.';
      } else {
        // Hashear contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Insertar nuevo usuario con rol CLIENTE
        $stmt = db()->prepare("
          INSERT INTO usuario (username, password, rol_id, email)
          VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('ssis', $username, $hash, $rolId, $email);

        if ($stmt->execute()) {
          $success = 'Cuenta creada exitosamente. Inicia sesión con tus credenciales.';
        } else {
          $error = 'Error al crear la cuenta. Intenta de nuevo.';
        }
      }
    }
  }
}

require __DIR__ . '/../html/register.html.php';