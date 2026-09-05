<?php
require __DIR__ . '/config.php';
// Página pública: no requiere login, cualquiera que escanee el QR entra acá.

$error = '';
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre'] ?? '');
    $documento = trim($_POST['documento'] ?? '');
    $localidad = trim($_POST['localidad'] ?? '');
    $telefono  = trim($_POST['telefono'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $talento   = trim($_POST['talento'] ?? '');

    if ($nombre === '' || $documento === '' || $localidad === '' || $telefono === '' || $email === '' || $talento === '') {
        $error = 'Completá todos los campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El email no es válido.';
    } else {
        try {
            $stmt = $conn->prepare('INSERT INTO inscripciones (nombre, documento, localidad, telefono, email, talento) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('ssssss', $nombre, $documento, $localidad, $telefono, $email, $talento);
            $stmt->execute();
            $ok = true;
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                // Choca con la clave única del documento
                $error = 'Ese documento ya está inscripto.';
            } else {
                $error = 'Ocurrió un error al guardar la inscripción. Probá de nuevo.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Inscripción · Go Talent</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="wrap narrow">
  <div class="login-card">
    <h1>Go <span style="color:#f4f2ff">Talent</span></h1>
    <p class="sub">Inscripción de participantes</p>

    <?php if ($ok): ?>
      <div class="aviso">¡Listo! Tu inscripción quedó registrada correctamente.</div>
    <?php else: ?>

      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" novalidate>
        <label for="nombre">Nombre completo</label>
        <input type="text" id="nombre" name="nombre" required autofocus value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">

        <label for="documento">Documento</label>
        <input type="text" id="documento" name="documento" required value="<?= htmlspecialchars($_POST['documento'] ?? '') ?>">

        <label for="localidad">Localidad</label>
        <input type="text" id="localidad" name="localidad" required value="<?= htmlspecialchars($_POST['localidad'] ?? '') ?>">

        <label for="telefono">Teléfono</label>
        <input type="text" id="telefono" name="telefono" required value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">

        <label for="email">Email</label>
        <input type="text" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label for="talento">Talento</label>
        <input type="text" id="talento" name="talento" required placeholder="Ej: canto, baile, magia..." value="<?= htmlspecialchars($_POST['talento'] ?? '') ?>">

        <button type="submit" class="btn-block">Inscribirme</button>
      </form>

    <?php endif; ?>
  </div>
</div>
</body>
</html>
