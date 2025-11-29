<!DOCTYPE html>
<html lang="es">

<head>
    <?php include 'app/views/inc/head.php'; ?>
</head>

<body data-page="inicio">
    <div id="app">

        <?php include 'app/views/inc/sidebar.php'; ?>

        <div id="main">
            <?php include 'app/views/inc/header.php'; ?>

            <!-- TÍTULO DE LA PÁGINA -->
            <div class="page-heading">
                <h3>Panel de Control - Torneos de Fútbol</h3>
                <p class="text-muted">
                    Resumen general de los torneos, equipos, partidos y jugadores.
                </p>
            </div>

            <div class="page-content">
                <section class="section">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>TORNEOS</span>
                            <button type="button" id="nuevo" class="btn btn-outline-success btn-admin"
                                data-bs-toggle="modal" data-bs-target="#modalEmpresa">
                                + Nuevo Torneo
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped w-100" id="tablaTorneos">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Fecha de Inicio</th>
                                        <th>Fecha de Fin</th>
                                        <th>Estado</th>
                                        <th class="text-center notexport">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
                
                <div class="modal fade" id="modalEmpresa" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title mb-0">Registrar torneo</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>

                            <div class="modal-body">
                                <form id="formTorneo" class="form form-vertical" method="post">
                                    <input type="hidden" id="idtorneo" name="idtorneo">
                                    <div class="form-body">
                                        <div class="row">
                                            <!-- NOMBRE DEL TORNEO -->
                                            <div class="col-12 mb-3">
                                                <div class="form-group has-icon-left">
                                                    <label for="nombre" id="titulo">Nombre del torneo</label>
                                                    <div class="position-relative">
                                                        <input type="text" class="form-control" id="nombre" name="nombre"
                                                            placeholder="Ej. Torneo Apertura 2025" required minlength="2">
                                                        <div class="form-control-icon">
                                                            <i class="bi bi-trophy"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- FECHA DE INICIO -->
                                            <div class="col-12 mb-3">
                                                <div class="form-group has-icon-left">
                                                    <label for="fecha_inicio">Fecha de inicio</label>
                                                    <div class="position-relative">
                                                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                                                        <div class="form-control-icon">
                                                            <i class="bi bi-calendar-event"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- FECHA DE FIN -->
                                            <div class="col-12 mb-3">
                                                <div class="form-group has-icon-left">
                                                    <label for="fecha_fin">Fecha de fin</label>
                                                    <div class="position-relative">
                                                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                                                        <div class="form-control-icon">
                                                            <i class="bi bi-calendar-check"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- BOTONES -->
                                            <div class="col-12 d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" name="accion" id="guardar" value="Guardar" class="btn btn-primary accion">
                                                    Guardar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <?php include 'app/views/inc/footer.php'; ?>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.33/dist/sweetalert2.all.min.js"></script>
    <?php include 'app/views/inc/script.php'; ?>
    <script src="app/ajax/torneo.js"></script>
</body>

</html>