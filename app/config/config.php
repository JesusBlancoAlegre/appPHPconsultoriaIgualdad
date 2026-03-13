<?php
/**
 * Configuración general de la aplicación
 */

// Entorno
define('APP_ENV', 'development');  // 'development' o 'production'
define('DEBUG', APP_ENV === 'development');

// URLs base
define('BASE_URL', 'http://localhost:8000');
define('UPLOADS_DIR', dirname(__DIR__) . '/storage/uploads/');

// Configuración de sesión
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
session_set_cookie_params([
    'lifetime' => 3600,  // 1 hora
    'path'     => '/',
    'domain'   => $_SERVER['HTTP_HOST'] ?? 'localhost',
    'secure'   => false,  // true en HTTPS
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Configuración de errores
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', dirname(__DIR__) . '/logs/error.log');
}

// Timezone
date_default_timezone_set('Europe/Madrid');

// Incluir configuración de BD
require __DIR__ . '/database.php';

// Retornar PDO
return $pdo;
?>