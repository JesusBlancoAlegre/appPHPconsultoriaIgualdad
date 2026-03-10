<?php
declare(strict_types=1);

session_start();
require __DIR__ . '/config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'ADMINISTRADOR') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Datos de ejemplo (puedes obtener de la BD después)
$datos = [
    'labels' => ['Contabilidad', 'Recursos Humanos', 'Marketing', 'Ventas', 'IT', 'Legal'],
    'datasets' => [
        [
            'label' => 'Hombres',
            'data' => [10, 15, 12, 18, 14, 20],
            'borderColor' => 'rgb(75, 192, 192)',
            'backgroundColor' => 'rgba(75, 192, 192, 0.3)',
            'borderWidth' => 2,
            'fill' => true,
            'tension' => 0.3
        ],
        [
            'label' => 'Mujeres',
            'data' => [5, 8, 10, 12, 11, 15],
            'borderColor' => 'rgb(54, 162, 235)',
            'backgroundColor' => 'rgba(54, 162, 235, 0.3)',
            'borderWidth' => 2,
            'fill' => true,
            'tension' => 0.3
        ]
    ]
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($datos);