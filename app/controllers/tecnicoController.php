<?php
require_once __DIR__ . "/../models/tecnicoModel.php";

$tecnico = new TecnicoModel();
$opcion = $_REQUEST["opcion"] ?? "";

switch ($opcion) {

    case "listar":
        $data = $tecnico->listar();
        echo json_encode(["status" => "success", "data" => $data]);
        break;

    case "agregar":
        $nombre = $_POST["nombre"] ?? "";
        $nacionalidad = $_POST["nacionalidad"] ?? "";

        $ok = $tecnico->agregar($nombre, $nacionalidad);
        echo json_encode([
            "status" => $ok ? "success" : "error",
            "message" => $ok ? "Técnico registrado correctamente." : "No se pudo registrar."
        ]);
        break;

    case "obtener":
        $id = $_GET["id"] ?? 0;
        $data = $tecnico->obtener($id);
        echo json_encode(["status" => "success", "data" => $data]);
        break;

    case "editar":
        $id = $_POST["id"] ?? 0;
        $nombre = $_POST["nombre"] ?? "";
        $nacionalidad = $_POST["nacionalidad"] ?? "";

        $ok = $tecnico->editar($id, $nombre, $nacionalidad);
        echo json_encode([
            "status" => $ok ? "success" : "error",
            "message" => $ok ? "Técnico actualizado correctamente." : "No se pudo actualizar."
        ]);
        break;

    case "eliminar":
        $id = $_POST["id"] ?? 0;

        $ok = $tecnico->eliminar($id);
        echo json_encode([
            "status" => $ok ? "success" : "error",
            "message" => $ok ? "Técnico eliminado." : "No se pudo eliminar."
        ]);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Operación no válida"]);
}
