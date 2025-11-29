<!DOCTYPE html>
<html lang="es">

<head>
    <?php include 'app/views/inc/head.php'; ?>
</head>

<body data-page="estadio">
    <div id="app">

        <?php include 'app/views/inc/sidebar.php'; ?>

        <div id="main">
            <?php include 'app/views/inc/header.php'; ?>

            <!-- TÍTULO DE LA PÁGINA -->
            <div class="page-heading">
                <h3>Gestión de Estadios</h3>
                <p class="text-muted">Administra los estadios registrados en el sistema.</p>
            </div>

            <div class="page-content">
                <section class="section">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>ESTADIOS</span>
                            <button type="button" id="nuevo" class="btn btn-outline-success btn-admin"
                                data-bs-toggle="modal" data-bs-target="#modalEstadio">
                                + Nuevo Estadio
                            </button>
                        </div>

                        <div class="card-body">
                            <table class="table table-striped w-100" id="tablaEstadios">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Ubicación</th>
                                        <th>Capacidad</th>
                                        <th class="text-center notexport">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- MODAL -->
                <div class="modal fade" id="modalEstadio" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title mb-0">Registrar Estadio</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Cerrar"></button>
                            </div>

                            <div class="modal-body">
                                <form id="formEstadio" class="form form-vertical" method="post">
                                    <input type="hidden" id="id" name="id">

                                    <div class="form-body">
                                        <div class="row">

                                            <!-- NOMBRE -->
                                            <div class="col-12 mb-3">
                                                <div class="form-group has-icon-left">
                                                    <label for="nombre">Nombre del Estadio</label>
                                                    <div class="position-relative">
                                                        <input type="text" class="form-control" id="nombre" name="nombre"
                                                            placeholder="Ej. Estadio Cuscatlán" required minlength="2">
                                                        <div class="form-control-icon">
                                                            <i class="bi bi-building"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- UBICACIÓN -->
                                            <div class="col-12 mb-3">
                                                <div class="form-group has-icon-left">
                                                    <label for="ubicacion">Ubicación</label>
                                                    <div class="position-relative">
                                                        <input type="text" class="form-control" id="ubicacion" name="ubicacion"
                                                            placeholder="Ej. San Salvador" required minlength="2">
                                                        <div class="form-control-icon">
                                                            <i class="bi bi-geo-alt"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- CAPACIDAD -->
                                            <div class="col-12 mb-3">
                                                <div class="form-group has-icon-left">
                                                    <label for="capacidad">Capacidad</label>
                                                    <div class="position-relative">
                                                        <input type="number" class="form-control" id="capacidad"
                                                            name="capacidad" placeholder="Ej. 35000" required min="0">
                                                        <div class="form-control-icon">
                                                            <i class="bi bi-people"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- BOTONES -->
                                            <div class="col-12 d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-light-secondary"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">
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

    <!-- LIBRERÍAS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php include 'app/views/inc/script.php'; ?>
    <script src="app/ajax/estadio.js"></script>
</body>

</html>
