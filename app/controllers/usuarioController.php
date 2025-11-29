<?php

header('Content-Type: application/json; charset=utf-8');
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . "/../models/usuarioModel.php";
require_once __DIR__ . "/../models/encriptarModel.php";

$usuarioModel = new Usuario();

$opcion = isset($_GET["opcion"]) ? trim($_GET["opcion"]) : null;

$response = ["status" => "error", "message" => "Opción inválida"];

try {
    switch ($opcion) {
        case "ingresar":
            $email = isset($_POST["email"]) ? trim($_POST["email"]) : null;
            $clave = isset($_POST["clave"]) ? trim($_POST["clave"]) : null;

            if (empty($email) || empty($clave)) {
                $response = ["status" => "error", "message" => "Email y clave son obligatorios"];
                break;
            }

            $emailEncriptado = encriptarModel::openCypher("encrypt", $email);
            $claveEncriptada = encriptarModel::openCypher("encrypt", $clave);

            $usuario = $usuarioModel->getUsuarioLogin($emailEncriptado, $claveEncriptada);

            if ($usuario) {
                session_start();
                $_SESSION["usuario"] = $usuario;
                $response = ["status" => "success", "usuario" => $usuario];
            } else {
                $response = ["status" => "error", "message" => "Email o clave incorrectos"];
            }
            break;

        case "registrar":
            $email = isset($_POST["email"]) ? trim($_POST["email"]) : null;
            $clave = isset($_POST["clave"]) ? trim($_POST["clave"]) : null;

            if (empty($email) || empty($clave)) {
                $response = [
                    "status" => "error",
                    "message" => "Email y clave son obligatorios"
                ];
                break;
            }

            $emailEncriptado = encriptarModel::openCypher("encrypt", $email);
            $claveEncriptada = encriptarModel::openCypher("encrypt", $clave);

            $ok = $usuarioModel->insertarUsuario($emailEncriptado, $claveEncriptada, "CLIENTE");

            if ($ok) {
                $response = [
                    "status"  => "success",
                    "message" => "Usuario registrado correctamente"
                ];
            } else {
                $response = [
                    "status"  => "error",
                    "message" => "No se pudo registrar el usuario"
                ];
            }
            break;

        case "cerrar":
            session_start();
            session_destroy();
            $response = ["status" => "success", "message" => "Sesión cerrada exitosamente"];
            break;

        default:
            $response = ["status" => "error", "message" => "Opción no válida"];
            break;
    }
} catch (Throwable $e) {
    error_log("Error en usuarioController: " . $e->getMessage());
    $response = ["status" => "error", "message" => "Ocurrió un error en el sistema"];
}

echo json_encode($response);
