<!DOCTYPE html>
<html lang="es">

<head>
    <?php include 'app/views/inc/head.php'; ?>
</head>

<body data-page="tecnicos">
<div id="app">

    <?php include 'app/views/inc/sidebar.php'; ?>

    <div id="main">
        <?php include 'app/views/inc/header.php'; ?>

        <!-- TÍTULO DE LA PÁGINA -->
        <div class="page-heading">
            <h3>Gestión de Técnicos</h3>
            <p class="text-muted">Administra los técnicos registrados en el sistema.</p>
        </div>

        <div class="page-content">
            <section class="section">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>TÉCNICOS</span>
                        <button type="button" id="nuevo" class="btn btn-outline-success btn-admin"
                                data-bs-toggle="modal" data-bs-target="#modalTecnico">
                            + Nuevo Técnico
                        </button>
                    </div>

                    <div class="card-body">
                        <table class="table table-striped w-100" id="tablaTecnicos">
                            <thead>
                                <tr>
                                    <!-- ID oculto -->
                                    <th style="display:none;">ID</th>
                                    <th>Nombre</th>
                                    <th>Nacionalidad</th>
                                    <th class="text-center notexport">Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- =======================
                 MODAL DE TÉCNICO
            ========================= -->
            <div class="modal fade" id="modalTecnico" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title mb-0">Registrar Técnico</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body">
                            <form id="formTecnico" class="form form-vertical" method="POST">
                                <input type="hidden" id="id" name="id">

                                <div class="form-body">
                                    <div class="row">

                                        <!-- NOMBRE -->
                                        <div class="col-12 mb-3">
                                            <div class="form-group has-icon-left">
                                                <label for="nombre">Nombre</label>
                                                <div class="position-relative">
                                                    <input type="text" class="form-control" id="nombre" name="nombre"
                                                           placeholder="Ej. Juan Carlos Chavez" required>
                                                    <div class="form-control-icon">
                                                        <i class="bi bi-person"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- NACIONALIDAD -->
                                        <div class="col-12 mb-3">
                                            <div class="form-group has-icon-left">
                                                <label for="nacionalidad">Nacionalidad</label>
                                                <div class="position-relative">
                                                    <input type="text" class="form-control" id="nacionalidad" name="nacionalidad"
                                                           placeholder="Ej. México" required>
                                                    <div class="form-control-icon">
                                                        <i class="bi bi-flag"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- BOTONES -->
                                        <div class="col-12 d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                                Cancelar
                                            </button>

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

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php include 'app/views/inc/script.php'; ?>
<script src="app/ajax/tecnico.js"></script>

</body>
</html>
