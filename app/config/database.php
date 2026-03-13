<?php
/**
 * Configuración de conexión a la Base de Datos
 * Usa PDO para mayor seguridad y compatibilidad
 */

// Credenciales de BD
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_NAME', 'igualdad');
define('DB_USER', 'root');
define('DB_PASS', '');  // Cambia según tu configuración
define('DB_CHARSET', 'utf8mb4');

// DSN (Data Source Name)
$dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

// Opciones de PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Crear conexión
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Log de conexión exitosa (opcional)
    // error_log("Conexión a BD exitosa: " . DB_NAME);
    
} catch (PDOException $e) {
    // Mostrar error (en producción, log instead)
    die("Error de conexión a BD: " . $e->getMessage());
}

// Retornar la conexión global
return $pdo;
?>