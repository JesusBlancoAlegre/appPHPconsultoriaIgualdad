<?php
// ============================================================
// auth.php
// ============================================================
// Archivo de utilidades para:
//  - iniciar/usar la sesión (session_start)
//  - proteger páginas que requieren login
//  - proteger páginas por rol (ADMINISTRADOR / CLIENTE / TECNICO, etc.)
// ============================================================

declare(strict_types=1);

// Iniciar sesión
session_start();

/**
 * ------------------------------------------------------------
 * require_login()
 *  - Comprueba si existe $_SESSION['user'] (se crea tras un login correcto).
 *  - Si NO existe, redirige al login y termina la ejecución.
 *
 * Por qué:
 *  - Evita que un usuario no autenticado acceda a páginas internas.
 */
function require_login(): void {
  if (!isset($_SESSION['user'])) {
    // Redirige al formulario de login
    header('Location: login.php');
    exit;
  }
}

/**
 * require_role($role)
 * Protege páginas que requieren un rol concreto.
 * Qué hace:
 *  1) Llama a require_login() (si no está logueado, lo manda a login).
 *  2) Lee el rol actual desde la sesión.
 *  3) Si el rol actual NO coincide, devuelve 403 y corta ejecución.
 */
function require_role(string $role): void {
  // Asegurarse de que hay sesión iniciada y usuario autenticado
  require_login();

  // Leer rol del usuario desde la sesión
  $current = (string)($_SESSION['user']['rol'] ?? '');

  // Validar rol
  if ($current !== $role) {
    // HTTP 403 = Forbidden
    http_response_code(403);
    exit('Acceso denegado');
  }
}