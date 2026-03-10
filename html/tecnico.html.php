<!doctype html>
<html lang="es">
<head>

  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Panel Técnico</title>

  <!--BOOTSTRAP -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- CSS  -->
  <link rel="stylesheet" href="../css/tecnico.css">
</head>

<body>

  <div class="container py-5">

    <div class="glass panel mx-auto p-4 float-in">

      <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
        <div class="d-flex align-items-center gap-3">
          <div class="brand-badge">TE</div>

          <div>
            <h4 class="mb-0">Panel Técnico</h4>
            <div class="text-muted small">
              Sesión: <strong><?= h($techUsername ?? 'tecnico') ?></strong>
            </div>
          </div>
        </div>


        <a class="btn btn-sm btn-logout" href="logout.php">Cerrar sesión</a>
      </div>

      <!--  SELECTOR DE CLIENTE  -->
      <form method="get" action="tecnico.php" class="row g-2 align-items-end mb-4 section-card p-3">
        <div class="col-12 col-md-6">
          <label class="form-label">Cliente</label>

          <select name="cliente_id" class="form-select" onchange="this.form.submit()">
            <?php foreach ($clientes as $c): ?>
              <option value="<?= (int)$c['id'] ?>" <?= ((int)$c['id'] === (int)$clienteId) ? 'selected' : '' ?>>
                <?= h($c['username']) ?>
              </option>
            <?php endforeach; ?>
          </select>

          <div class="form-text">Selecciona un cliente para ver sus avisos y archivos.</div>
        </div>
        <div class="col-12 col-md-6 text-md-end">
          <noscript><button class="btn btn-primary" type="submit">Ver</button></noscript>
        </div>
      </form>

      <div class="row g-3">

        <!--  BLOQUE IZQUIERDO AVISOS -->
        <div class="col-12 col-lg-6">
          <div class="section-card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="mb-0">Avisos del cliente</h6>

              <!-- Conteo de avisos -->
              <span class="badge text-bg-secondary"><?= (int)count($avisos) ?></span>
            </div>

            <!-- Si no hay avisos, mostrar texto; si hay, mostrar tabla -->
            <?php if (empty($avisos)): ?>
              <div class="text-muted small">No hay avisos para este cliente.</div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                  <thead>
                    <tr>
                      <th>Tipo</th>
                      <th>Mensaje</th>
                      <th>Fecha/hora</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php foreach ($avisos as $a):
                      /**
                       * COLOR DEL BADGE SEGÚN EL TIPO DE AVISO
                       * - login  -> verde (success)
                       * - error  -> rojo (danger)
                       * - upload -> azul (primary)
                       * - resto  -> gris (secondary)
                       */
                      $tipo = (string)($a['tipo'] ?? 'info');
                      $badge = 'text-bg-secondary';
                      if ($tipo === 'login')  $badge = 'text-bg-success';
                      if ($tipo === 'error')  $badge = 'text-bg-danger';
                      if ($tipo === 'upload') $badge = 'text-bg-primary';
                    ?>
                      <tr>
                        <td><span class="badge <?= h($badge) ?>"><?= h($tipo) ?></span></td>
                        <td><?= h($a['mensaje'] ?? '') ?></td>
                        <td class="mono"><?= h($a['creado_en'] ?? '') ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- BLOQUE DERECHO: ARCHIVOS -->
        <div class="col-12 col-lg-6">
          <div class="section-card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="mb-0">Archivos subidos</h6>

              <!-- Conteo de archivos -->
              <span class="badge text-bg-secondary"><?= (int)count($archivos) ?></span>
            </div>

            <?php if (empty($archivos)): ?>
              <div class="text-muted small">No hay archivos subidos.</div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                  <thead>
                    <tr>
                      <th>Archivo</th>
                      <th>Tamaño</th>
                      <th>Subido</th>
                      <th></th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php foreach ($archivos as $f): ?>
                      <tr>
                        <!-- Nombre original que subió el usuario -->
                        <td><?= h($f['nombre_original'] ?? '') ?></td>

                        <!-- formatBytes: helper que convierte bytes a KB/MB/GB -->
                        <td class="mono"><?= h(formatBytes((int)($f['tamano_bytes'] ?? 0))) ?></td>

                        <!-- Fecha/hora de subida -->
                        <td class="mono"><?= h($f['subido_en'] ?? '') ?></td>

                        <!-- Descargar: pasa el id del registro en BD -->
                        <td class="text-end">
                          <a class="btn btn-outline-primary btn-sm" href="download.php?id=<?= (int)$f['id'] ?>">Descargar</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="text-center small text-light opacity-75 mt-3">
        © <?= date('Y') ?> Panel Técnico
      </div>

      <div class="text-muted small mt-3">
        Nota: Aquí podemos añadir instrucciones para revisar avisos/archivos.
      </div>

    </div>
  </div>

</body>
</html>