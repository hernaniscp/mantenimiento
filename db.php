<?php
// ==========================================================
// CONFIGURACIÓN DE CONEXIÓN A LA BASE DE DATOS
// Completá estos 4 datos con los que te dio tu hosting / cPanel.
// ==========================================================

$host     = "localhost";              // normalmente es "localhost"
$db_name  = "nombre_de_tu_base";      // ej: "colegio_mantenimiento"
$usuario  = "tu_usuario_mysql";       // el usuario que te dieron para la BD
$password = "tu_contraseña_mysql";    // la contraseña de ese usuario

$conexion = new mysqli($host, $usuario, $password, $db_name);

if ($conexion->connect_error) {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    die(json_encode(["error" => "Error de conexión a la base de datos: " . $conexion->connect_error]));
}

$conexion->set_charset("utf8mb4");
?>
