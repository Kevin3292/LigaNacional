<?php
session_start();
require_once __DIR__ . "/../models/modelTabla.php";

if (!isset($_SESSION['torneo']['id'])) {
    echo json_encode(["data" => []]);
    exit;
}

$model = new PosicionesModel();
$idTorneo = $_SESSION['torneo']['id'];
$opcion = $_REQUEST['opcion'] ?? '';

switch ($opcion) {
    case 'listar':
        $data = $model->obtenerTabla($idTorneo);

        if ($data) {
            foreach ($data as &$row) {
                // Calcular Diferencia de Goles
                $row['DG'] = $row['GF'] - $row['GC'];

                // Procesar Imagen
                if (!empty($row['imagen'])) {
                    $row['imagen'] = "data:image/jpeg;base64," . base64_encode($row['imagen']);
                } else {
                    $row['imagen'] = "public/img/sin-escudo.png";
                }
            }
        }

        echo json_encode(["data" => $data ? $data : []]);
        break;
}