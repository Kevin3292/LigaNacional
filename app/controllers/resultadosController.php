<?php
session_start();
require_once __DIR__ . "/../models/resultadosModel.php";

if (!isset($_SESSION['torneo']['id'])) {
    echo json_encode(["status" => "error", "message" => "Sin torneo"]);
    exit;
}

$model = new ResultadosModel();
$idTorneo = $_SESSION['torneo']['id'];
$opcion = $_REQUEST['opcion'] ?? '';

switch ($opcion) {
    case 'simular':
        $idJornada = $_POST['id_jornada'];
        
        // 2. Simular
        $res = $model->simularJornada($idJornada);
        echo json_encode($res);
        break;
}