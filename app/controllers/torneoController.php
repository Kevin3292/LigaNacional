<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . "/../models/torneoModel.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$torneoModel = new Torneo();

$opcion = isset($_GET["opcion"]) ? trim($_GET["opcion"]) : null;

$response = ["status" => "error", "message" => "Opción inválida"];

try {
    switch ($opcion) {

        case "registrar":
            // 1. Recoger datos
            $nombre       = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : null;
            $fecha_inicio = isset($_POST["fecha_inicio"]) ? trim($_POST["fecha_inicio"]) : null;
            $fecha_fin    = isset($_POST["fecha_fin"]) ? trim($_POST["fecha_fin"]) : null;

            // NOTA: He quitado $estado de la llamada porque en el Modelo definimos 
            // que se guardara por defecto en false/0, pero si lo necesitas, agrégalo al modelo también.

            // 2. Llamar al modelo
            // El modelo ya devuelve ["status" => "...", "message" => "..."]
            // y YA hizo la validación de fechas internamente.
            $response = $torneoModel->registrarTorneo($nombre, $fecha_inicio, $fecha_fin);

            // ¡Listo! $response ya trae si fue éxito o error y el porqué.
            break;

        case "editar":
            $id           = isset($_POST["idtorneo"]) ? (int)$_POST["idtorneo"] : 0;
            $nombre       = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : null;
            $fecha_inicio = isset($_POST["fecha_inicio"]) ? trim($_POST["fecha_inicio"]) : null;
            $fecha_fin    = isset($_POST["fecha_fin"]) ? trim($_POST["fecha_fin"]) : null;
            $estado       = isset($_POST["estado"]) ? (int)$_POST["estado"] : 1;

            if ($id <= 0) {
                $response = ["status" => "error", "message" => "ID de torneo inválido"];
                break;
            }

            // SIMPLIFICACIÓN:
            // Pasamos la responsabilidad de la respuesta al Modelo.
            // El modelo valida fechas, actualiza y nos dice qué pasó.
            $response = $torneoModel->editarTorneo($id, $nombre, $fecha_inicio, $fecha_fin, $estado);

            break;

        case "traerTorneos":
            $torneos  = $torneoModel->obtenerTorneos();
            $response = ["status" => "success", "data" => $torneos];
            break;

        case "mostrar":
            $id     = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
            $torneo = $torneoModel->obtenerTorneo($id);

            if ($torneo) {
                if (isset($torneo[0]) && is_array($torneo[0])) {
                    $torneo = $torneo[0];
                }

                $response = ["status" => "success", "data" => $torneo];
            } else {
                $response = ["status" => "error", "message" => "Torneo No Encontrado"];
            }
            break;

        case "seleccionar":
            $id = isset($_POST["id"]) ? (int)$_POST["id"] : 0;

            if ($id <= 0) {
                $response = ["status" => "error", "message" => "ID de torneo inválido"];
                break;
            }

            $torneo = $torneoModel->obtenerTorneo($id);

            if ($torneo) {
                // Si viene de fetchAll(), tomar la primera fila
                if (isset($torneo[0]) && is_array($torneo[0])) {
                    $torneo = $torneo[0];
                }

                // ESTA ES LA ASIGNACIÓN BUENA
                $_SESSION['torneo'] = [
                    'id'     => $torneo['id'],
                    'nombre' => $torneo['nombre'],
                ];

                $response = [
                    "status"  => "success",
                    "message" => "Torneo seleccionado",
                    // "debugSession" => $_SESSION  // <- puedes descomentar para ver en JS
                ];
            } else {
                $response = ["status" => "error", "message" => "Torneo no encontrado"];
            }
            break;

        case "salir":
            // Solo “olvidar” el torneo actual, NO cerrar la sesión del usuario
            unset($_SESSION['torneo']);

            $response = [
                "status"  => "success",
                "message" => "Torneo deseleccionado correctamente"
            ];
            break;

        default:
            $response = ["status" => "error", "message" => "Opción no válida"];
            break;
    }
} catch (Throwable $e) {
    error_log("Error en torneoController: " . $e->getMessage());
    $response = ["status" => "error", "message" => "Ocurrió un error en el sistema"];
}

echo json_encode($response);
