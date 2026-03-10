<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Crear cuenta</title>

  <!--  BOOTSTRAP  -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!--CSS PERSONALIZADO -->
  <link rel="stylesheet" href="../css/login.css">
</head>

<body>
  <div class="container py-4 py-lg-5">
    <div class="row justify-content-center align-items-center g-4">
      <div class="col-12 col-lg-7">
        <div class="side-illustration">
          <!-- Imagen de igualdad  -->
          <img src="../assets/igualdad.png" alt="Igualdad de género">
        </div>
      </div>
      <div class="col-12 col-lg-5">
        <div class="glass p-4 p-sm-4">

          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="brand-badge">+</div>
            <div>
              <h4 class="mb-0">Crear cuenta</h4>
              <div class="text-muted small">Únete como cliente</div>
            </div>
          </div>

          <!--MENSAJE DE ERROR -->
          <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 mb-3">
              <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
          <?php endif; ?>

          <!--MENSAJE DE ÉXITO -->
          <?php if (!empty($success)): ?>
            <div class="alert alert-success py-2 mb-3">
              <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div class="text-center">
              <a href="login.php" class="btn btn-primary">Ir al login</a>
            </div>
          <?php else: ?>

            <!--FORMULARIO DE REGISTRO-->
            <form method="post" action="register.php" class="vstack gap-3">

              <!-- Email -->
              <div>
                <label class="form-label">Email</label>
                <input class="form-control" name="email" type="email" required>
              </div>

              <!-- Usuario -->
              <div>
                <label class="form-label">Usuario</label>
                <input class="form-control" name="username" minlength="3" required>
                <small class="form-text text-muted">Mínimo 3 caracteres</small>
              </div>

              <!-- Contraseña -->
              <div>
                <label class="form-label">Contraseña</label>
                <input class="form-control" name="password" type="password" minlength="6" required>
                <small class="form-text text-muted">Mínimo 6 caracteres</small>
              </div>

              <!-- Confirmar Contraseña -->
              <div>
                <label class="form-label">Confirmar contraseña</label>
                <input class="form-control" name="password_confirm" type="password" minlength="6" required>
              </div>

              <!-- Botón de submit -->
              <button class="btn btn-brand w-100 py-2" type="submit">Crear cuenta</button>

              <!-- Link al login -->
              <div class="text-center small">
                ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
              </div>
            </form>

          <?php endif; ?>

          <div class="text-center small text-muted mt-4">
            © <?= date('Y') ?> Consultoría Igualdad
          </div>
        </div>
      </div>

    </div>
  </div>

</body>
</html>