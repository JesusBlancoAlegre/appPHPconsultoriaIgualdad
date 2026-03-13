<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 3) {
    die("Acceso no autorizado (falta sesión/rol)");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir Registro Retributivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Subir Registro Retributivo (Excel)</h5>
                    </div>
                    <div class="card-body">
                        <!-- ✅ APUNTA AL CONTROLADOR -->
                        <form action="<?php echo $_SERVER['BASE_URL']; ?>../controllers/procesar_registro_retributivo.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Archivo Excel (.xlsx)</label>
                                <input 
                                    type="file" 
                                    name="excel" 
                                    class="form-control"
                                    accept=".xlsx"
                                    required>
                            </div>

                            <div class="alert alert-info">
                                ✓ Solo se permiten archivos Excel (.xlsx)
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Subir y procesar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>