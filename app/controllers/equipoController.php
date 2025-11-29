<?php
session_start();
require_once __DIR__ . "/../models/equipoModel.php";

if (!isset($_SESSION['torneo']) || !isset($_SESSION['torneo']['id'])) {
    // Si no hay torneo seleccionado, devolvemos error y data vacía
    echo json_encode([
        "status" => "error", 
        "message" => "No hay un torneo seleccionado.",
        "data" => [] 
    ]);
    exit;
}

// 2. OBTENER EL ID CORRECTAMENTE
// Aquí estaba el error. Ahora accedemos a la estructura correcta.
$idTorneo = $_SESSION['torneo']['id'];

$equipo = new EquipoModel();
$opcion = $_REQUEST["opcion"] ?? "";

switch ($opcion) {

    case "listar":
        $datos = $equipo->listarPorTorneo($idTorneo);
        
        // --- CORRECCIÓN CRÍTICA: PROCESAR IMÁGENES BLOB ---
        // json_encode falla con binarios. Debemos convertir a Base64.
        $dataArray = [];
        
        if ($datos) {
            foreach ($datos as $row) {
                // Si hay imagen, la convertimos. Si no, ponemos null o default.
                if (!empty($row['imagen'])) {
                    $row['imagen'] = "data:image/jpeg;base64," . base64_encode($row['imagen']);
                } else {
                    $row['imagen'] = ""; // O URL a una imagen por defecto
                }
                $dataArray[] = $row;
            }
        }

        // Enviamos el array procesado
        echo json_encode([
            "data" => $dataArray
        ]);
        break;

    case "agregar":
        if (!isset($_FILES["imagen"]) || $_FILES["imagen"]["error"] !== UPLOAD_ERR_OK) {
             // Si no es obligatoria, puedes manejarlo, pero aquí asumimos que sí.
             // Puedes pasar NULL si la imagen es opcional.
             $imagen = null; 
        } else {
             $imagen = file_get_contents($_FILES["imagen"]["tmp_name"]);
        }

        // Llamamos al modelo (que ahora devuelve un ARRAY con status y message)
        $respuesta = $equipo->agregar(
            $_POST["nombre"],
            $_POST["ciudad"],
            $_POST["fuerza"],
            $_POST["id_estadio"],
            $_POST["id_tecnico"],
            $imagen,
            $idTorneo
        );

        // --- CORRECCIÓN: DEVOLVER LA RESPUESTA DEL MODELO DIRECTAMENTE ---
        // El modelo ya nos dice si fue "success" o "error" (ej. "Copa llena")
        echo json_encode($respuesta);
        break;

    

    case "obtener":
        $data = $equipo->obtener($_GET["id"]);
        if ($data && !empty($data["imagen"])) {
            $data["imagen"] = "data:image/jpeg;base64," . base64_encode($data["imagen"]);
        }
        echo json_encode(["status" => "success", "data" => $data]);
        break;

    case "editar":
        // Lógica para imagen en editar:
        // Si el usuario NO sube nueva imagen, $imagen debe ser null o manejarse en el modelo
        // para no borrar la existente.
        $imagen = null;
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
            $imagen = file_get_contents($_FILES["imagen"]["tmp_name"]);
        }

        // Nota: Tu método editar en el modelo debería saber qué hacer si $imagen es null
        // (usualmente: no actualizar esa columna).
        $respuesta = $equipo->editar(
            $_POST["id"],
            $_POST["nombre"],
            $_POST["ciudad"],
            $_POST["fuerza"],
            $_POST["id_estadio"],
            $_POST["id_tecnico"],
            $imagen
        );

        echo json_encode($respuesta);
        break;

    case "eliminar":
        // RECORDATORIO: Cambia esto por "retirarDelTorneo" si seguiste mi consejo anterior
        $ok = $equipo->eliminar($_POST["id"]); 
        echo json_encode([
            "status" => $ok ? "success" : "error",
            "message" => $ok ? "Equipo eliminado." : "No se pudo eliminar."
        ]);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Operación no válida."]);
}
?>