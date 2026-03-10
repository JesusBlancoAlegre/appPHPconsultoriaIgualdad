<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Panel Administrador</title>
    <!--CSS-->
    <link rel="stylesheet" href="../css/admin.css">

    <!--BOOTSTRAP-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
</head>

<body>

    <div class="container py-5">
        <div class="card panel mx-auto shadow-sm border-0">
            <div class="card-body p-4">

                <!--  Muestra el username de sesión -->
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <h4 class="mb-1">Panel de Administrador</h4>
                        <div class="text-muted small">
                            Sesión: <strong><?= htmlspecialchars($adminUsername ?? 'admin', ENT_QUOTES, 'UTF-8') ?></strong>
                        </div>
                    </div>

                    <!-- Logout -->
                    <a class="btn btn-outline-secondary btn-sm" href="logout.php">Cerrar sesión</a>
                </div>

                <!-- MENSAJES FLASH POR GET-->
                <?php if (!empty($_GET['msg'])): ?>
                    <div class="alert alert-info py-2"><?= h($_GET['msg']) ?></div>
                <?php endif; ?>
                <?php if (($view ?? 'menu') === 'menu'): ?>
                    <div class="vstack gap-3">

                        <!-- agregar usuarios -->
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-center mb-2">Agregar usuarios</h6>
                            <a class="btn btn-primary w-100" href="admin.php?view=add">Agregar usuario</a>
                        </div>

                        <!-- editar usuarios -->
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-center mb-2">Editar usuarios</h6>
                            <a class="btn btn-warning w-100" href="admin.php?view=edit">Editar usuario</a>
                        </div>

                        <!-- eliminar usuarios -->
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-center mb-2">Eliminar usuarios</h6>
                            <a class="btn btn-danger w-100" href="admin.php?view=delete">Eliminar usuario</a>
                        </div>

                        <!-- GRÁFICO (CHART.JS)-->
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-center mb-2">Gráfico de Datos</h6>
                            <canvas id="areaChart" style="max-height: 250px;"></canvas>
                        </div>


                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-center mb-2">Contratar</h6>
                            <button type="button" class="btn btn-primary w-100">Contratar</button>
                        </div>

                    </div>

                <?php elseif ($view === 'add'): ?>
                    <!-- AGREGAR USUARIO-->
                    <h6 class="text-center mb-3">Agregar usuario</h6>

                    <form method="post" action="admin_controller.php" class="vstack gap-2">
                        <input type="hidden" name="accion" value="crear">

                        <!-- Username -->
                        <div>
                            <label class="form-label">Nombre de usuario</label>
                            <input class="form-control" name="username" required>
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="form-label">Contraseña</label>
                            <input class="form-control" name="password" type="password" required>
                        </div>

                        <!-- Rol-->
                        <div>
                            <label class="form-label">Rol</label>
                            <select class="form-select" name="rol_id" required>
                                <option value="">-- seleccionar --</option>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= (int)$r['id'] ?>"><?= h($r['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Acciones -->
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary w-100" type="submit">Crear</button>
                            <a class="btn btn-outline-secondary w-100" href="admin.php">Volver</a>
                        </div>
                    </form>


                <?php elseif ($view === 'edit'): ?>
                    <!-- EDITAR USUARIOS-->
                    <h6 class="text-center mb-3">Editar usuarios</h6>

                    <?php
                    $selectedId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                    $selectedUser = null;
                    foreach ($usuarios as $uu) {
                        if ((int)$uu['id'] === $selectedId) {
                            $selectedUser = $uu;
                            break;
                        }
                    }

                    // Si no hay id en la URL o no se encontró, se selecciona el primero de la lista
                    if ($selectedUser === null && !empty($usuarios)) {
                        $selectedUser = $usuarios[0];
                        $selectedId = (int)$selectedUser['id'];
                    }
                    ?>

                    <!-- Selector de usuario (GET) -->
                    <form method="get" action="admin.php" class="mb-3">

                        <input type="hidden" name="view" value="edit">

                        <label class="form-label mb-1">Selecciona usuario</label>

                        <select class="form-select" name="id" onchange="this.form.submit()" required>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?= (int)$u['id'] ?>" <?= ((int)$u['id'] === (int)$selectedId) ? 'selected' : '' ?>>
                                    <?= h($u['username']) ?> (<?= h($u['rol'] ?? '') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <noscript><button class="btn btn-primary w-100 mt-2" type="submit">Cargar</button></noscript>
                    </form>

                    <?php if ($selectedUser === null): ?>
                        <!-- Si no hay usuarios -->
                        <div class="alert alert-warning">No hay usuarios para editar.</div>
                        <a class="btn btn-outline-secondary w-100" href="admin.php">Volver</a>
                    <?php else: ?>

                        <!-- Formulario de edición -->
                        <form method="post" action="admin_controller.php" class="border rounded bg-light p-3">
                            <input type="hidden" name="accion" value="editar">

                            <input type="hidden" name="id" value="<?= (int)$selectedUser['id'] ?>">

                            <!-- Username -->
                            <div class="mb-2">
                                <label class="form-label mb-1">Username</label>
                                <input class="form-control" name="username" value="<?= h($selectedUser['username']) ?>" required>
                            </div>

                            <!-- Rol -->
                            <div class="mb-2">
                                <label class="form-label mb-1">Rol</label>
                                <select class="form-select" name="rol_id" required>
                                    <?php foreach ($roles as $r): ?>
                                        <option value="<?= (int)$r['id'] ?>" <?= ((int)$r['id'] === (int)$selectedUser['rol_id']) ? 'selected' : '' ?>>
                                            <?= h($r['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Password  -->
                            <div class="mb-2">
                                <label class="form-label mb-1">Nueva contraseña (opcional)</label>
                                <input class="form-control" name="password" type="password" placeholder="(dejar vacío)">
                            </div>

                            <!-- Acciones -->
                            <div class="d-flex gap-2">
                                <button class="btn btn-warning w-100" type="submit">Guardar cambios</button>
                                <a class="btn btn-outline-secondary w-100" href="admin.php">Volver</a>
                            </div>
                        </form>
                    <?php endif; ?>



                <?php elseif ($view === 'delete'): ?>
                    <!-- VISTA: ELIMINAR USUARIOS -->
                    <h6 class="text-center mb-3">Eliminar usuarios</h6>

                    <div class="vstack gap-2">
                        <?php foreach ($usuarios as $u): ?>
                            <form method="post" action="admin_controller.php" class="border rounded bg-light p-2"
                                onsubmit="return confirm('¿Seguro que quieres eliminar el usuario <?= h($u['username']) ?>?');">

                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">

                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div><strong><?= h($u['username']) ?></strong></div>
                                        <div class="text-muted small"><?= h($u['rol'] ?? '') ?></div>
                                    </div>
                                    <button class="btn btn-danger" type="submit">Eliminar</button>
                                </div>
                            </form>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-3">
                        <a class="btn btn-outline-secondary w-100" href="admin.php">Volver</a>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>


    <script>
        /*DATOS GRAFICO */
        fetch('../php/grafico.php')
            .then(res => res.json())
            .then(data => {
                const ctx = document.getElementById('areaChart');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'line',
                        data: data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            })
            .catch(err => console.error('Error cargando gráfico:', err));
    </script>
</body>

</html>