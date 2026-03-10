<?php
declare(strict_types=1);

//Funciones helper (pueden ir aquí o en un includes/helpers.php)
function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function formatBytes(int $bytes): string {
  $u = ['B','KB','MB','GB','TB']; $i=0; $v=(float)$bytes;
  while ($v >= 1024 && $i < count($u)-1) { $v/=1024; $i++; }
  return round($v, 2) . ' ' . $u[$i];
}

//Aquí recibes/cargas lo que te llegue por PHP 
//Datos de prueba
$events = [
  ['type'=>'login', 'message'=>'Cliente ha iniciado sesión', 'ts'=>'2026-03-09 10:12:33'],
  ['type'=>'error', 'message'=>'Error: permiso denegado', 'ts'=>'2026-03-09 10:15:02'],
];

$uploads = [
  ['name'=>'parte_1.pdf', 'size'=>345678, 'uploaded_at'=>'2026-03-09 10:20:10', 'download'=>'download.php?file=parte_1.pdf'],
  ['name'=>'foto.jpg',    'size'=>91234,  'uploaded_at'=>'2026-03-09 10:21:45', 'download'=>'download.php?file=foto.jpg'],
];

$updatedAt = date('Y-m-d H:i:s');

//Renderizar la vista (HTML)
require __DIR__ . '/../html/tecnico.html.php';