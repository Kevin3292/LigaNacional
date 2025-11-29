<?php

// --- SESIÓN Y DATOS DEL TORNEO ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no hay usuario logueado, redirige al login (ajusta si tu ruta es distinta)
if (empty($_SESSION['usuario'])) {
    header("Location: " . APP_URL . "login");
    exit;
}

// Si no hay torneo seleccionado, redirige a la lista de torneos
if (empty($_SESSION['torneo']['id'])) {
    header("Location: " . APP_URL . "inicio");
    exit;
}

$torneoId     = $_SESSION['torneo']['id'];
$torneoNombre = $_SESSION['torneo']['nombre'] ?? 'Torneo sin nombre';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php include 'app/views/inc/head.php'; ?>
</head>

<body data-page="torneo-inicio">
    <div id="app">

        <?php include 'app/views/inc/sidebar.php'; ?>

        <div id="main">
            <?php include 'app/views/inc/header.php'; ?>

            <!-- TÍTULO DE LA PÁGINA -->
            <div class="page-heading">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h3>Panel del Torneo</h3>
                        <p class="text-muted mb-0">
                            Estás trabajando en el torneo:
                            <strong><?= htmlspecialchars($torneoNombre, ENT_QUOTES, 'UTF-8'); ?></strong>
                        </p>
                    </div>
                    <span class="badge bg-primary">
                        ID: <?= (int)$torneoId; ?>
                    </span>
                </div>
            </div>

            <div class="page-content">
                <section class="section">
                    <div class="row">
                        <!-- Card principal del torneo -->
                        <div class="col-12 col-lg-8 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h5 class="card-title mb-2">
                                        <i class="bi bi-trophy-fill me-1"></i>
                                        <?= htmlspecialchars($torneoNombre, ENT_QUOTES, 'UTF-8'); ?>
                                    </h5>
                                    <p class="text-muted mb-2">
                                        Desde el menú lateral puedes administrar toda la información de este torneo:
                                    </p>
                                    <ul class="mb-0">
                                        <li>Equipos</li>
                                        <li>Técnicos</li>
                                        <li>Estadios</li>
                                        <li>Jugadores</li>
                                        <li>Jornadas</li>
                                        <li>Partidos</li>
                                        <li>Tabla de posiciones</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Card de resumen rápido (para stats futuras) -->
                        <div class="col-12 col-lg-4 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h6 class="card-title mb-2">
                                        <i class="bi bi-speedometer2 me-1"></i>
                                        Resumen rápido
                                    </h6>
                                    <p class="text-muted small mb-2">
                                        Más adelante puedes mostrar aquí estadísticas del torneo.
                                    </p>
                                    <ul class="list-unstyled small text-muted mb-0">
                                        <li>• Equipos registrados: <span id="resEquipos">–</span></li>
                                        <li>• Partidos jugados: <span id="resPartidos">–</span></li>
                                        <li>• Próximo partido: <span id="resProximo">–</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>
            </div>

            <?php include 'app/views/inc/footer.php'; ?>

        </div>
    </div>

    <?php include 'app/views/inc/script.php'; ?>
    <!-- Si en el futuro quieres JS específico para este dashboard, lo agregas aquí -->
    <!-- <script src="app/ajax/torneo_inicio.js"></script> -->
</body>

</html>
