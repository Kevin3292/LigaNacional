<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'app/views/inc/head.php'; ?>
</head>
<body data-page="jornadas">
    <div id="app">
        <?php include 'app/views/inc/sidebar.php'; ?>
        <div id="main">
            <?php include 'app/views/inc/header.php'; ?>

            <div class="page-heading">
                <h3>Gestión de Jornadas</h3>
                <p class="text-muted">Control de fechas y partidos del torneo.</p>
            </div>

            <div class="page-content">
                <section class="section">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>LISTADO DE JORNADAS</span>
                            
                            <div id="btn-container">
                                <button class="btn btn-primary btn-admin" onclick="confirmarGenerarFixture()">
                                    <i class="bi bi-calendar-plus"></i> Generar Fixture
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped" id="tablaJornadas" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Jornada</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>

            <div class="modal fade" id="modalPartidos" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="tituloModalPartidos">Partidos de la Jornada</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="tablaDetallePartidos">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha/Hora</th>
                                            <th class="text-end">Local</th>
                                            <th class="text-center"></th>
                                            <th>Visita</th>
                                            <th>Estadio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'app/views/inc/footer.php'; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php include 'app/views/inc/script.php'; ?>
    <script src="app/ajax/jornada.js?v=23"></script>
</body>
</html>