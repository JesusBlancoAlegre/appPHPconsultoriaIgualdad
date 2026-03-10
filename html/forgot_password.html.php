<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Recuperar contraseña</title>

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
            <div class="brand-badge">🔑</div>
            <div>
              <h4 class="mb-0">Recuperar contraseña</h4>
              <div class="text-muted small">Restablece tu acceso</div>
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
          <?php endif; ?>

          <?php if ($step === 'request'): ?>
            <!-- PASO 1: Solicitar recuperación -->
            <form method="post" action="forgot_password.php" class="vstack gap-3">
              <input type="hidden" name="action" value="request">

              <p class="text-muted small">Ingresa tu usuario y email para recibir instrucciones de recuperación.</p>

              <div>
                <label class="form-label">Usuario</label>
                <input class="form-control" name="username" required>
              </div>

              <div>
                <label class="form-label">Email</label>
                <input class="form-control" name="email" type="email" required>
              </div>

              <button class="btn btn-brand w-100 py-2" type="submit">Solicitar recuperación</button>

              <div class="text-center small">
                <a href="login.php">Volver al login</a>
              </div>
            </form>

          <?php elseif ($step === 'reset'): ?>
            <!-- PASO 2: Cambiar contraseña -->
            <form method="post" action="forgot_password.php" class="vstack gap-3">
              <input type="hidden" name="action" value="reset">

              <p class="text-muted small">Ingresa el token y tu nueva contraseña.</p>

              <div>
                <label class="form-label">Token</label>
                <input class="form-control" name="token" required>
              </div>

              <div>
                <label class="form-label">Nueva contraseña</label>
                <input class="form-control" name="password" type="password" minlength="6" required>
              </div>

              <div>
                <label class="form-label">Confirmar contraseña</label>
                <input class="form-control" name="password_confirm" type="password" minlength="6" required>
              </div>

              <button class="btn btn-brand w-100 py-2" type="submit">Cambiar contraseña</button>
            </form>

          <?php elseif ($step === 'success'): ?>
            <!-- ÉXITO -->
            <div class="text-center">
              <div class="alert alert-success">
                ✓ Contraseña actualizada exitosamente
              </div>
              <a href="login.php" class="btn btn-primary w-100">Ir al login</a>
            </div>

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