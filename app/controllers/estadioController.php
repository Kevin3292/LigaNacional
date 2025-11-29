<?php
require_once __DIR__ . "/../models/estadioModel.php";

$estadio = new estadioModel();

$opcion = $_REQUEST['opcion'] ?? '';

switch ($opcion) {
    case 'listar':
        $data = $estadio->listar();
        echo json_encode(['status' => 'success', 'data' => $data]);
        break;

    case 'agregar':
        $nombre = $_POST['nombre'] ?? '';
        $ubicacion = $_POST['ubicacion'] ?? '';
        $capacidad = $_POST['capacidad'] ?? 0;
        $ok = $estadio->agregar($nombre, $ubicacion, $capacidad);
        echo json_encode(['status' => $ok ? 'success' : 'error']);
        break;

    case 'obtener':
        $id = $_GET['id'] ?? 0;
        $data = $estadio->obtener($id);
        echo json_encode(['status' => 'success', 'data' => $data]);
        break;

    case 'editar':
        $id = $_POST['id'] ?? 0;
        $nombre = $_POST['nombre'] ?? '';
        $ubicacion = $_POST['ubicacion'] ?? '';
        $capacidad = $_POST['capacidad'] ?? 0;
        $ok = $estadio->editar($id, $nombre, $ubicacion, $capacidad);
        echo json_encode(['status' => $ok ? 'success' : 'error']);
        break;

    case 'eliminar':
        $id = $_POST['id'] ?? 0;
        $ok = $estadio->eliminar($id);
        echo json_encode(['status' => $ok ? 'success' : 'error']);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Operación no válida']);
        break;
}
