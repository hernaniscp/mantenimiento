<?php
require __DIR__ . '/config.php';
$jurado = requerir_login();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: lista.php');
    exit;
}

// Traigo al participante
$stmt = $conn->prepare("SELECT id, NOMBRE, LOCALIDAD, DOCUMENTO, TELEFONO, EMAIL, TALENTO FROM participantes WHERE id = ? AND ESTADO = '1' LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$participante = $stmt->get_result()->fetch_assoc();

if (!$participante) {
    header('Location: lista.php');
    exit;
}

// Si este jurado ya lo votó, no lo dejo entrar de nuevo
$stmt = $conn->prepare('SELECT id FROM votos WHERE id_jurado = ? AND id_participante = ? LIMIT 1');
$stmt->bind_param('ii', $jurado['id'], $id);
$stmt->execute();
$yaVoto = $stmt->get_result()->fetch_assoc();

if ($yaVoto) {
    header('Location: lista.php?ya_votado=1');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $puntaje = (int)($_POST['puntaje'] ?? -1);

    if ($puntaje < 1 || $puntaje > 10) {
        $error = 'El puntaje tiene que ser un número entero entre 1 y 10.';
    } else {
        try {
            $stmt = $conn->prepare('INSERT INTO votos (id_jurado, id_participante, puntaje) VALUES (?, ?, ?)');
            $stmt->bind_param('iii', $jurado['id'], $id, $puntaje);
            $stmt->execute();
            header('Location: lista.php?votado=1');
            exit;
        } catch (mysqli_sql_exception $e) {
            // Choca con el índice único: ya lo había votado (doble clic, etc.)
            header('Location: lista.php?ya_votado=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($participante['NOMBRE']) ?> · Go Talent</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="topbar">
  <div class="brand">Go <span>Talent</span></div>
  <div>
    <span class="jurado-tag">Jurado: <strong><?= htmlspecialchars($jurado['nombre']) ?></strong></span>
    · <a class="logout-link" href="logout.php">Salir</a>
  </div>
</div>

<div class="wrap narrow">
  <a class="back-link" href="lista.php">&larr; Volver a la lista</a>

  <div class="ficha">
    <span class="talento-tag"><?= htmlspecialchars($participante['TALENTO']) ?></span>
    <h1><?= htmlspecialchars($participante['NOMBRE']) ?></h1>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="datos-grid">
      <div class="dato">
        <label>Documento</label>
        <div class="valor"><?= htmlspecialchars((string)$participante['DOCUMENTO']) ?></div>
      </div>
      <div class="dato">
        <label>Localidad</label>
        <div class="valor"><?= htmlspecialchars($participante['LOCALIDAD']) ?></div>
      </div>
      <div class="dato">
        <label>Teléfono</label>
        <div class="valor"><?= htmlspecialchars($participante['TELEFONO']) ?></div>
      </div>
      <div class="dato">
        <label>Email</label>
        <div class="valor"><?= htmlspecialchars($participante['EMAIL']) ?></div>
      </div>
      <div class="dato">
        <label>Talento</label>
        <div class="valor"><?= htmlspecialchars($participante['TALENTO']) ?></div>
      </div>
    </div>

    <form method="post" class="puntaje-form" novalidate>
      <label for="puntaje">Puntaje</label>
      <input type="number" id="puntaje" name="puntaje" min="1" max="10" step="1" required autofocus>
      <p class="escala-nota">Escala del 1 al 10. Una vez enviado, no se puede modificar.</p>
      <button type="submit" class="btn-block">Confirmar voto</button>
    </form>
  </div>
</div>
</body>
</html>
