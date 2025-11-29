<?php
require_once "./config/app.php";
require_once "./autoload.php";

use app\controllers\viewsController;

// Iniciar sesión una sola vez
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// slug solicitado (primera parte de ?views=)
$slug = isset($_GET['views']) ? explode("/", $_GET['views'])[0] : 'login';

/**
 * Rutas públicas: se pueden ver sin iniciar sesión
 * (normalmente solo login y 404).
 */
$PUBLICO = ['login', 'registro', '404'];

/**
 * Rutas que requieren que haya un torneo seleccionado
 * en $_SESSION['torneo'].
 */
$REQUIERE_TORNEO = [
    'torneo-inicio',
    'equipos',
    'tecnicos',
    'estadios',
    'jugadores',
    'jornadas',
    'tabla'
];

$viewsController = new viewsController();
$vista = $viewsController->obtenerVistasControlador($slug);

// Si ya resolvió 404, mostramos y salimos.
if ($vista === "app/views/content/404.php") {
    require_once $vista;
    exit;
}

// ¿Hay usuario logueado?
$estaLogueado = !empty($_SESSION['usuario']);

// Si el usuario YA está logueado y pide login, mejor mandarlo al inicio (lista de torneos)
if ($estaLogueado && in_array($slug, ['login', 'registro'], true)) {
    $vista = $viewsController->obtenerVistasControlador('inicio');
    require_once $vista;
    exit;
}

// Si NO está logueado y la vista NO es pública → obligar a login
if (!$estaLogueado && !in_array($slug, $PUBLICO, true)) {
    $vista = $viewsController->obtenerVistasControlador('login');
    require_once $vista;
    exit;
}

// A partir de aquí, o es público, o ya está logueado

// Si la vista es de un torneo específico, validar que haya torneo seleccionado
if (in_array($slug, $REQUIERE_TORNEO, true)) {
    $hayTorneo = !empty($_SESSION['torneo']['id']);

    if (!$hayTorneo) {
        // Sin torneo seleccionado, mándalo a la lista de torneos (inicio)
        $vista = $viewsController->obtenerVistasControlador('inicio');
    }
}

require_once $vista;
