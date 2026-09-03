<?php
require __DIR__ . '/config.php';

// Si ya está logueado, va directo a la lista
if (!empty($_SESSION['jurado_id'])) {
    header('Location: lista.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $pass    = $_POST['pass'] ?? '';

    if ($usuario === '' || $pass === '') {
        $error = 'Completá usuario y contraseña.';
    } else {
        $stmt = $conn->prepare('SELECT id, nombre, pass FROM jurado WHERE usuario = ? LIMIT 1');
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $jurado = $result->fetch_assoc();
        $stmt->close();

        if ($jurado && password_verify($pass, $jurado['pass'])) {
            $_SESSION['jurado_id']     = $jurado['id'];
            $_SESSION['jurado_nombre'] = $jurado['nombre'];
            header('Location: lista.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ingreso Jurado · Go Talent</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="wrap narrow">
  <div class="login-card">
    <h1>Go <span style="color:#f4f2ff">Talent</span></h1>
    <p class="sub">Ingreso de jurados</p>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
      <label for="usuario">Usuario</label>
      <input type="text" id="usuario" name="usuario" autocomplete="username" required autofocus>

      <label for="pass">Contraseña</label>
      <input type="password" id="pass" name="pass" autocomplete="current-password" required>

      <button type="submit" class="btn-block">Ingresar</button>
    </form>
  </div>
</div>
</body>
</html>
