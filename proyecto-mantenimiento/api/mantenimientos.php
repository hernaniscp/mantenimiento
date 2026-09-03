<?php
// ==========================================================
// API de mantenimientos: responde a la página web (script.js)
// GET    -> devuelve la lista de mantenimientos (con el nombre del equipo)
// POST   -> registra un mantenimiento nuevo
// PUT    -> actualiza el estado (ej: marcar como realizado)
// DELETE -> elimina un mantenimiento por id
// ==========================================================

header("Content-Type: application/json; charset=UTF-8");

require_once "db.php";

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        obtenerMantenimientos($conexion);
        break;
    case 'POST':
        agregarMantenimiento($conexion);
        break;
    case 'PUT':
        actualizarMantenimiento($conexion);
        break;
    case 'DELETE':
        eliminarMantenimiento($conexion);
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
}

function obtenerMantenimientos($conexion) {
    $resultado = $conexion->query(
        "SELECT m.id, m.equipo_id, e.nombre AS equipo_nombre,
                e.fecha_registro AS equipo_fecha_alta,
                m.tipo_trabajo, m.descripcion, m.estado, m.fecha
         FROM mantenimientos m
         INNER JOIN equipos e ON e.id = m.equipo_id
         ORDER BY m.fecha DESC, m.id DESC"
    );

    $mantenimientos = [];
    while ($fila = $resultado->fetch_assoc()) {
        $mantenimientos[] = $fila;
    }

    echo json_encode($mantenimientos);
}

function agregarMantenimiento($conexion) {
    $datos = json_decode(file_get_contents("php://input"), true);

    $equipo_id    = intval($datos['equipo_id'] ?? 0);
    $tipo_trabajo = trim($datos['tipo_trabajo'] ?? '');
    $descripcion  = trim($datos['descripcion'] ?? '');
    $estado       = trim($datos['estado'] ?? 'pendiente');
    $fecha        = trim($datos['fecha'] ?? '');

    if ($equipo_id <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "Debés seleccionar un equipo"]);
        return;
    }

    if ($tipo_trabajo === '') {
        http_response_code(400);
        echo json_encode(["error" => "El tipo de trabajo es obligatorio"]);
        return;
    }

    if ($fecha === '') {
        $fecha = date('Y-m-d');
    }

    if ($estado !== 'pendiente' && $estado !== 'realizado') {
        $estado = 'pendiente';
    }

    // Verificar que el equipo exista antes de insertar
    $verificar = $conexion->prepare("SELECT id FROM equipos WHERE id = ?");
    $verificar->bind_param("i", $equipo_id);
    $verificar->execute();
    if ($verificar->get_result()->num_rows === 0) {
        http_response_code(400);
        echo json_encode(["error" => "El equipo seleccionado no existe"]);
        $verificar->close();
        return;
    }
    $verificar->close();

    $stmt = $conexion->prepare(
        "INSERT INTO mantenimientos (equipo_id, tipo_trabajo, descripcion, estado, fecha)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("issss", $equipo_id, $tipo_trabajo, $descripcion, $estado, $fecha);

    if ($stmt->execute()) {
        echo json_encode([
            "id"           => $stmt->insert_id,
            "equipo_id"    => $equipo_id,
            "tipo_trabajo" => $tipo_trabajo,
            "descripcion"  => $descripcion,
            "estado"       => $estado,
            "fecha"        => $fecha
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo guardar el mantenimiento"]);
    }

    $stmt->close();
}

function actualizarMantenimiento($conexion) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $datos = json_decode(file_get_contents("php://input"), true);
    $estado = trim($datos['estado'] ?? '');

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "ID inválido"]);
        return;
    }

    if ($estado !== 'pendiente' && $estado !== 'realizado') {
        http_response_code(400);
        echo json_encode(["error" => "Estado inválido"]);
        return;
    }

    $stmt = $conexion->prepare("UPDATE mantenimientos SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $estado, $id);

    if ($stmt->execute()) {
        echo json_encode(["ok" => true]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar el mantenimiento"]);
    }

    $stmt->close();
}

function eliminarMantenimiento($conexion) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "ID inválido"]);
        return;
    }

    $stmt = $conexion->prepare("DELETE FROM mantenimientos WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["ok" => true]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar el mantenimiento"]);
    }

    $stmt->close();
}
?>
