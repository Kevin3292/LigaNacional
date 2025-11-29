<?php
session_start();
require_once __DIR__ . "/../models/generadorModel.php";
require_once __DIR__ . "/../models/torneoModel.php";

// Validar Sesión
if (!isset($_SESSION['torneo']) || !isset($_SESSION['torneo']['id'])) {
    echo json_encode(["status" => "error", "message" => "No hay torneo seleccionado"]);
    exit;
}

$fixtureModel = new FixtureModel();
$torneoModel = new Torneo();

$idTorneo = $_SESSION['torneo']['id'];
$opcion = $_REQUEST["opcion"] ?? "";

switch ($opcion) {

    case "generar":
        // 1. BUSCAMOS LA FECHA REAL DEL TORNEO EN LA BD
        // Nota: Asumo que en tu Torneo.php tienes un método obtenerTorneo($id)
        // que devuelve un array con 'fechainicio'
        $datosTorneo = $torneoModel->obtenerTorneo($idTorneo);

        // obtenerTorneo suele devolver un array de filas, tomamos la primera [0]
        // Si tu método devuelve directo la fila, quita el [0]
        $fechaInicioBD = $datosTorneo[0]['fechainicio'];

        // 2. VALIDAMOS QUE HAYA FECHA
        if (!$fechaInicioBD) {
            echo json_encode(["status" => "error", "message" => "El torneo no tiene fecha de inicio configurada."]);
            exit;
        }

        // 3. GENERAMOS EL FIXTURE USANDO ESA FECHA
        $respuesta = $fixtureModel->generarFixture($idTorneo, $fechaInicioBD);
        echo json_encode($respuesta);
        break;

    case "listar_jornadas":
        $datos = $fixtureModel->listarJornadas($idTorneo);
        echo json_encode(["data" => $datos ? $datos : []]);
        break;

    case "listar_partidos_por_jornada":
        if (!isset($_GET['id_jornada'])) {
            echo json_encode(["data" => []]);
            exit;
        }

        $datos = $fixtureModel->obtenerPartidosPorJornada($_GET['id_jornada']);

        // PROCESAR IMÁGENES Y FECHAS
        if ($datos) {
            foreach ($datos as &$p) {
                // Formato de Fecha
                $date = new DateTime($p['fecha']);
                $p['fecha_hora'] = $date->format('d/m H:i');

                // Imagen Local
                if (!empty($p['img_local'])) {
                    $p['img_local'] = "data:image/jpeg;base64," . base64_encode($p['img_local']);
                } else {
                    $p['img_local'] = "public/img/sin-escudo.png"; // Ruta a una imagen default
                }

                // Imagen Visita
                if (!empty($p['img_visita'])) {
                    $p['img_visita'] = "data:image/jpeg;base64," . base64_encode($p['img_visita']);
                } else {
                    $p['img_visita'] = "public/img/sin-escudo.png";
                }
            }
        }

        echo json_encode(["data" => $datos ? $datos : []]);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Opción inválida"]);
}
