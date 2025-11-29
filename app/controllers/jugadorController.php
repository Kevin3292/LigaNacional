<?php
error_reporting(0);
ob_start();
session_start();
require_once __DIR__ . "/../models/JugadorModel.php";

$jugadorModel = new JugadorModel();
$opcion = $_REQUEST['opcion'] ?? '';
$response = ["data" => []];

try {
    switch ($opcion) {
        
        case "listar":
            $idEquipo = isset($_GET['id_equipo']) ? $_GET['id_equipo'] : '';
            if (!empty($idEquipo) && $idEquipo != "null") {
                $data = $jugadorModel->listarPorEquipo($idEquipo);
            } else {
                $data = $jugadorModel->listar();
            }
            $response = ["data" => $data ? $data : []];
            break;

        case "agregar":
            // --- CORRECCIÓN CRÍTICA DE ORDEN ---
            // El modelo espera: ($nombre, $dorsal, $posicion, $nacionalidad, $goles, $titular, $id_equipo)
            $res = $jugadorModel->agregar(
                $_POST['nombre'], 
                $_POST['dorsal'], 
                $_POST['posicion'], 
                $_POST['nacionalidad'], // Nacionalidad va CUARTO
                $_POST['goles'],        // Goles va QUINTO
                isset($_POST['titular']) ? 1 : 0, // Titular va SEXTO
                $_POST['id_equipo']     // ID Equipo va SÉPTIMO (Al final)
            );
            $response = $res ? ["status" => "success", "message" => "Jugador registrado"] : ["status" => "error", "message" => "Error al registrar"];
            break;
            
        case "editar":
            // --- CORRECCIÓN CRÍTICA DE ORDEN ---
            // El modelo espera: ($id, $nombre, $dorsal, $posicion, $nacionalidad, $goles, $titular, $id_equipo)
            $res = $jugadorModel->editar(
                $_POST['id'],
                $_POST['nombre'], 
                $_POST['dorsal'], 
                $_POST['posicion'], 
                $_POST['nacionalidad'],
                $_POST['goles'], 
                isset($_POST['titular']) ? 1 : 0,
                $_POST['id_equipo']
            );
            $response = $res ? ["status" => "success", "message" => "Jugador actualizado"] : ["status" => "error", "message" => "Error al actualizar"];
            break;

        case "eliminar":
             $res = $jugadorModel->eliminar($_POST['id']);
             $response = $res ? ["status" => "success", "message" => "Jugador eliminado"] : ["status" => "error", "message" => "Error al eliminar"];
             break;
             
        case "obtener":
             $data = $jugadorModel->obtener($_GET['id']);
             $response = ["status" => "success", "data" => $data];
             break;
    }

} catch (Exception $e) {
    $response = ["status" => "error", "message" => $e->getMessage(), "data" => []];
}

ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>