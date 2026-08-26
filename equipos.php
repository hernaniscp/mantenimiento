<?php
// ==========================================================
// API de equipos: responde a la página web (script.js)
// GET    -> devuelve la lista de equipos
// POST   -> agrega un equipo nuevo
// DELETE -> elimina un equipo por id
// ==========================================================

header("Content-Type: application/json; charset=UTF-8");

require_once "db.php";

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        obtenerEquipos($conexion);
        break;
    case 'POST':
        agregarEquipo($conexion);
        break;
    case 'DELETE':
        eliminarEquipo($conexion);
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
}

function obtenerEquipos($conexion) {
    $resultado = $conexion->query(
        "SELECT id, nombre, tipo, ubicacion, estado FROM equipos ORDER BY id DESC"
    );

    $equipos = [];
    while ($fila = $resultado->fetch_assoc()) {
        $equipos[] = $fila;
    }

    echo json_encode($equipos);
}

function agregarEquipo($conexion) {
    $datos = json_decode(file_get_contents("php://input"), true);

    $nombre    = trim($datos['nombre'] ?? '');
    $tipo      = trim($datos['tipo'] ?? '');
    $ubicacion = trim($datos['ubicacion'] ?? '');
    $estado    = trim($datos['estado'] ?? 'ok');

    if ($nombre === '') {
        http_response_code(400);
        echo json_encode(["error" => "El nombre del equipo es obligatorio"]);
        return;
    }

    if ($estado !== 'ok' && $estado !== 'pendiente') {
        $estado = 'ok';
    }

    $stmt = $conexion->prepare(
        "INSERT INTO equipos (nombre, tipo, ubicacion, estado) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssss", $nombre, $tipo, $ubicacion, $estado);

    if ($stmt->execute()) {
        echo json_encode([
            "id"        => $stmt->insert_id,
            "nombre"    => $nombre,
            "tipo"      => $tipo,
            "ubicacion" => $ubicacion,
            "estado"    => $estado
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo guardar el equipo"]);
    }

    $stmt->close();
}

function eliminarEquipo($conexion) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "ID inválido"]);
        return;
    }

    $stmt = $conexion->prepare("DELETE FROM equipos WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["ok" => true]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar el equipo"]);
    }

    $stmt->close();
}
?>
