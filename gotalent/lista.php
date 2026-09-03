<?php
require __DIR__ . '/config.php';
$jurado = requerir_login();

// Traigo participantes activos + si este jurado ya los calificó
$sql = "
    SELECT p.id, p.NOMBRE, p.LOCALIDAD, p.TALENTO,
           v.puntaje
    FROM participantes p
    LEFT JOIN votos v ON v.id_participante = p.id AND v.id_jurado = ?
    WHERE p.ESTADO = '1'
    ORDER BY p.NOMBRE ASC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $jurado['id']);
$stmt->execute();
$participantes = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Participantes · Go Talent</title>
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

<div class="wrap">
  <h2 class="page-title">Participantes</h2>
  <p class="page-sub">Elegí a quién calificar. Una vez que votes a un participante, no vas a poder volver a calificarlo.</p>

  <?php if (isset($_GET['votado'])): ?>
    <div class="aviso">Voto registrado correctamente.</div>
  <?php elseif (isset($_GET['ya_votado'])): ?>
    <div class="error">Ya calificaste a ese participante, no se puede votar dos veces.</div>
  <?php endif; ?>

  <?php if ($participantes->num_rows === 0): ?>
    <p style="color:var(--text-dim)">Todavía no hay participantes cargados.</p>
  <?php endif; ?>

  <?php while ($p = $participantes->fetch_assoc()): ?>
    <div class="participante-row">
      <div class="participante-info">
        <span class="talento"><?= htmlspecialchars($p['TALENTO']) ?></span>
        <div class="nombre"><?= htmlspecialchars($p['NOMBRE']) ?></div>
        <div class="localidad"><?= htmlspecialchars($p['LOCALIDAD']) ?></div>
      </div>

      <?php if ($p['puntaje'] !== null): ?>
        <span class="estado-votado">Calificado (<?= (int)$p['puntaje'] ?>/10)</span>
      <?php else: ?>
        <a class="btn" href="votar.php?id=<?= (int)$p['id'] ?>">Votar</a>
      <?php endif; ?>
    </div>
  <?php endwhile; ?>
</div>
</body>
</html>
