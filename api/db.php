<?php
// ==========================================================
// CONFIGURACIÓN DE CONEXIÓN A LA BASE DE DATOS
// Completá estos 4 datos con los que te dio tu hosting / cPanel.
// ==========================================================

$host     = "mon16.servidoraweb.net";              // normalmente es "localhost"
$db_name  = "conakwwi_mantenimiento";      // ej: "colegio_mantenimiento"
$usuario  = "conakwwi_pasantes";       // el usuario que te dieron para la BD
$password = "pasantes2026";    // la contraseña de ese usuario

$conexion = new mysqli($host, $usuario, $password, $db_name);

if ($conexion->connect_error) {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    die(json_encode(["error" => "Error de conexión a la base de datos: " . $conexion->connect_error]));
}

$conexion->set_charset("utf8mb4");
?>
