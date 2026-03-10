<!doctype html>
<html lang="es">
<head>

  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Panel Cliente</title>
<!-- CSS -->
  <link rel="stylesheet" href="../css/cliente.css">

  <!-- BOOTSTRAP-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container py-5">
    <div class="card panel mx-auto shadow-sm border-0">
      <div class="card-body p-4">

        <div class="d-flex align-items-start justify-content-between mb-3">
          <div>
            <h4 class="mb-1">Panel de Cliente</h4>
            <div class="text-muted small">
              Sesión: <strong><?= htmlspecialchars($clienteUsername ?? 'cliente', ENT_QUOTES, 'UTF-8') ?></strong>
            </div>
          </div>
          <a class="btn btn-outline-secondary btn-sm" href="logout.php">Cerrar sesión</a>
        </div>

        <!--  MENSAJE FLASH POR GET (?msg=...) -->
        <?php if (!empty($_GET['msg'])): ?>
          <div class="alert alert-info py-2">
            <?= htmlspecialchars($_GET['msg'], ENT_QUOTES, 'UTF-8') ?>
          </div>
        <?php endif; ?>

        <?php $view = $view ?? 'menu'; ?>

        <?php if ($view === 'menu'): ?>
          <div class="vstack gap-3">

            <!-- DATOS -->
            <div class="border rounded p-3 bg-light">
              <h6 class="text-center mb-2">Mis datos</h6>
              <a class="btn btn-primary w-100" href="cliente.php?view=perfil">Editar perfil</a>
            </div>

            <!-- SUBIR DOCUMENTOS-->
            <div class="border rounded p-3 bg-light">
              <h6 class="text-center mb-2">Subir documentación</h6>
              <a class="btn btn-success w-100" href="cliente.php?view=upload">Subir archivo</a>
              <div class="form-text text-center">Excel / Word (según lo permitido)</div>
            </div>

            <!-- VER DOCUMENTOS -->
            <div class="border rounded p-3 bg-light">
              <h6 class="text-center mb-2">Mis archivos</h6>
              <a class="btn btn-warning w-100" href="cliente.php?view=archivos">Ver archivos subidos</a>
            </div>

          </div>

        <?php elseif ($view === 'perfil'): ?>
          <!-- VISTA: EDITAR PERFIL -->
          <h6 class="text-center mb-3">Editar perfil</h6>

          <form method="post" action="cliente_controller.php" class="vstack gap-2">

            <input type="hidden" name="accion" value="editar">

            <!-- ID del cliente en sesión -->
            <input type="hidden" name="id" value="<?= (int)($clienteId ?? 0) ?>">

            <!-- Username -->
            <div>
              <label class="form-label">Username</label>
              <input
                class="form-control"
                name="username"
                value="<?= htmlspecialchars($clienteUsername ?? '', ENT_QUOTES, 'UTF-8') ?>"
                required
              >
            </div>

            <!-- Password  -->
            <div>
              <label class="form-label">Nueva contraseña (opcional)</label>
              <input class="form-control" name="password" type="password" placeholder="(dejar vacío)">
            </div>

            <!-- Botones -->
            <div class="d-flex gap-2">
              <button class="btn btn-primary w-100" type="submit">Guardar</button>
              <a class="btn btn-outline-secondary w-100" href="cliente.php">Volver</a>
            </div>
          </form>

        <?php elseif ($view === 'upload'): ?>
          <!--  VISTA: SUBIR ARCHIVOS -->
          <h6 class="text-center mb-3">Subir archivos</h6>

          <form action="upload.php" method="post" enctype="multipart/form-data" class="vstack gap-2">
            <div>
              <label class="form-label" for="fileToUpload">Seleccione archivos Excel/Word</label>

              <input
                type="file"
                class="form-control"
                name="fileToUpload[]"
                id="fileToUpload"
                accept=".xlsx,.xls,.docx,.doc"
                multiple
                required
              >

              <div class="form-text">
                Solo .xlsx .xls .docx .doc (puedes seleccionar varios archivos con Ctrl/Shift)
              </div>
            </div>

            <!-- Submit: dispara la subida a upload.php -->
            <button type="submit" class="btn btn-success w-100" name="submit" value="submit">
              Subir Archivos
            </button>

            <!-- Volver al menú -->
            <a class="btn btn-outline-secondary w-100" href="cliente.php">Volver</a>
          </form>

        <?php elseif ($view === 'archivos'): ?>
          <!--  VISTA: LISTA DE ARCHIVOS SUBIDOS -->
          <h6 class="text-center mb-3">Mis archivos</h6>

          <?php if (empty($archivos)): ?>
            <div class="alert alert-secondary mb-3">Aún no has subido archivos.</div>
          <?php else: ?>
            <div class="list-group mb-3">
              <?php foreach ($archivos as $a): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div class="me-3">
                    <div class="fw-semibold">
                      <?= htmlspecialchars($a['nombre_original'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="small text-muted">
                      Subido: <?= htmlspecialchars($a['subido_en'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                  </div>

                  <!-- Descarga por ID (lo usa download.php para buscar el archivo en BD) -->
                  <a class="btn btn-outline-primary btn-sm" href="download.php?id=<?= (int)($a['id'] ?? 0) ?>">
                    Descargar
                  </a>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <!-- Volver al menú -->
          <a class="btn btn-outline-secondary w-100" href="cliente.php">Volver</a>

        <?php endif; ?>

      </div>
    </div>
  </div>


  <script>
    /**  DEBUG */
    document.getElementById('fileToUpload')?.addEventListener('change', function () {
      console.log('Archivos seleccionados:', this.files.length);
      for (let i = 0; i < this.files.length; i++) {
        console.log((i + 1) + '. ' + this.files[i].name + ' (' + this.files[i].size + ' bytes)');
      }
    });
  </script>
</body>
</html>