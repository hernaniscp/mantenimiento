<?php
require __DIR__ . '/config.php';

// Acceso separado del de los jurados: solo con la clave de administración.
if (!empty($_POST['admin_pass']) && $_POST['admin_pass'] === ADMIN_PASS) {
    $_SESSION['admin_ok'] = true;
}

if (empty($_SESSION['admin_ok'])) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resultados · Go Talent</title>
    <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
    <div class="wrap narrow">
      <div class="login-card">
        <h1>Resultados</h1>
        <p class="sub">Acceso solo para organización</p>
        <form method="post">
          <label for="admin_pass">Clave de administración</label>
          <input type="password" id="admin_pass" name="admin_pass" required autofocus>
          <button type="submit" class="btn-block">Ver resultados</button>
        </form>
      </div>
    </div>
    </body>
    </html>
    <?php
    exit;
}

$sql = "
    SELECT p.id, p.NOMBRE, p.LOCALIDAD, p.TALENTO,
           COUNT(v.id) AS cant_votos,
           COALESCE(SUM(v.puntaje), 0) AS puntaje_total
    FROM participantes p
    LEFT JOIN votos v ON v.id_participante = p.id
    WHERE p.ESTADO = '1'
    GROUP BY p.id, p.NOMBRE, p.LOCALIDAD, p.TALENTO
    ORDER BY puntaje_total DESC, cant_votos DESC
";
$participantes = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Resultados · Go Talent</title>
<link rel="stylesheet" href="css/style.css">
<style>
  table { width: 100%; border-collapse: collapse; margin-top: 20px; }
  th, td { text-align: left; padding: 12px 10px; border-bottom: 1px solid var(--line); }
  th { color: var(--text-dim); font-weight: 500; font-size: 0.85rem; }
  td.num { text-align: right; font-variant-numeric: tabular-nums; }
  .completo { color: var(--green); }
  .incompleto { color: var(--coral); }
</style>
</head>
<body>
<div class="topbar">
  <div class="brand">Go <span>Talent</span></div>
  <a class="logout-link" href="?salir=1">Salir</a>
</div>
<?php if (isset($_GET['salir'])) { unset($_SESSION['admin_ok']); header('Location: resultados.php'); exit; } ?>

<div class="wrap">
  <h2 class="page-title">Resultados</h2>
  <p class="page-sub">Suma de puntajes de los <?= TOTAL_JURADOS ?> jurados por participante.</p>

  <table>
    <thead>
      <tr>
        <th>Participante</th>
        <th>Talento</th>
        <th class="num">Votos</th>
        <th class="num">Puntaje total</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($p = $participantes->fetch_assoc()): ?>
        <?php $completo = (int)$p['cant_votos'] >= TOTAL_JURADOS; ?>
        <tr>
          <td><?= htmlspecialchars($p['NOMBRE']) ?><br><span style="color:var(--text-dim);font-size:0.8rem"><?= htmlspecialchars($p['LOCALIDAD']) ?></span></td>
          <td><?= htmlspecialchars($p['TALENTO']) ?></td>
          <td class="num <?= $completo ? 'completo' : 'incompleto' ?>"><?= (int)$p['cant_votos'] ?>/<?= TOTAL_JURADOS ?></td>
          <td class="num"><strong><?= (int)$p['puntaje_total'] ?></strong></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
