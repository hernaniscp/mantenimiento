<?php
/**
 * Go Talent - Alta de jurados
 * ---------------------------------------------------------------
 * Se usa UNA SOLA VEZ para cargar a los 5 jurados con su contraseña
 * ya encriptada (la tabla `jurado` NUNCA debe guardar contraseñas
 * en texto plano).
 *
 * Cómo usarlo:
 *  1. Completá el array $jurados de abajo con nombre, usuario y la
 *     contraseña que le vas a dar a cada jurado (en texto plano,
 *     acá nomás, para que este script la encripte).
 *  2. Subí este archivo a tu hosting (por ejemplo dentro de /gotalent/sql/)
 *     y abrilo una vez desde el navegador:
 *     https://tudominio.com/gotalent/sql/crear_jurados.php
 *  3. Vas a ver un listado confirmando el alta de cada jurado.
 *  4. IMPORTANTE: borrá este archivo del hosting después de usarlo,
 *     para que nadie más pueda volver a ejecutarlo.
 */

require __DIR__ . '/../config.php';

$jurados = [
    ['nombre' => 'Jurado 1', 'usuario' => 'jurado1', 'pass' => 'jurado1'],
    ['nombre' => 'Jurado 2', 'usuario' => 'jurado2', 'pass' => 'jurado2'],
    ['nombre' => 'Jurado 3', 'usuario' => 'jurado3', 'pass' => 'jurado3'],
    ['nombre' => 'Jurado 4', 'usuario' => 'jurado4', 'pass' => 'jurado4'],
    ['nombre' => 'Jurado 5', 'usuario' => 'jurado5', 'pass' => 'jurado5'],
];

header('Content-Type: text/plain; charset=utf-8');

$stmt = $conn->prepare('INSERT INTO jurado (nombre, usuario, pass) VALUES (?, ?, ?)');

foreach ($jurados as $j) {
    $hash = password_hash($j['pass'], PASSWORD_DEFAULT);
    $stmt->bind_param('sss', $j['nombre'], $j['usuario'], $hash);
    try {
        $stmt->execute();
        echo "OK  - {$j['nombre']} (usuario: {$j['usuario']})\n";
    } catch (mysqli_sql_exception $e) {
        echo "ERROR - {$j['nombre']} (usuario: {$j['usuario']}) -> " . $e->getMessage() . "\n";
    }
}

echo "\nListo. Borrá este archivo del servidor ahora.\n";
