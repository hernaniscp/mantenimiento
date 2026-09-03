<?php
/**
 * Go Talent - Configuración de conexión a la base de datos
 * Completá estos 4 datos con los de tu hosting (los mismos que usás
 * para entrar a phpMyAdmin).
 */

define('DB_HOST', 'mon16.servidoraweb.net');          // Host de la base (el mismo del dump)
define('DB_NAME', 'conakwwi_mantenimiento'); // Nombre de la base
define('DB_USER', 'conakwwi_pasantes');       // Usuario de MySQL
define('DB_PASS', 'pasantes2026');    // Contraseña de MySQL

// Cantidad total de jurados del concurso (se usa para calcular si un
// participante ya fue calificado por todos)
define('TOTAL_JURADOS', 5);

// Contraseña para entrar a la pantalla de resultados (resultados.php).
// Cambiala por una propia antes de subir el sitio.
define('jurados', 'jurados12345');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    die('No se pudo conectar a la base de datos. Revisá los datos en config.php.');
}

session_start();

/**
 * Corta la ejecución si no hay un jurado logueado y lo manda al login.
 */
function requerir_login(): array
{
    if (empty($_SESSION['jurado_id'])) {
        header('Location: login.php');
        exit;
    }
    return [
        'id'     => $_SESSION['jurado_id'],
        'nombre' => $_SESSION['jurado_nombre'],
    ];
}
