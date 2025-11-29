<!DOCTYPE html>
<html lang="es">

<head>
    <?php include 'app/views/inc/head.php'; ?>
</head>

<body data-page="jugadores">
    <div id="app">

        <?php include 'app/views/inc/sidebar.php'; ?>
        <div id="main">
            <?php include 'app/views/inc/header.php'; ?>

            <div class="page-heading">
                <h3>Gestión de Jugadores</h3>
                <p class="text-muted">Administra los jugadores de los equipos.</p>
            </div>

            <div class="page-content">
                <section class="section">

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>JUGADORES</span>
                            <button class="btn btn-outline-success btn-admin" data-bs-toggle="modal" data-bs-target="#modalJugador">
                                + Nuevo Jugador
                            </button>
                        </div>

                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Filtrar por Equipo:</label>
                                    <select id="filtroEquipo" class="form-select">
                                        <option value="">Ver Todos</option>
                                    </select>
                                </div>
                            </div>
                            <table class="table table-striped w-100" id="tablaJugadores">
                                <thead>
                                    <tr>
                                        <th style="display:none;">ID</th>
                                        <th>Nombre</th>
                                        <th>Dorsal</th>
                                        <th>Posición</th>
                                        <th>Equipo</th>
                                        <th>Goles</th>
                                        <th>Titular</th>
                                        <th class="notexport text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </section>

                <!-- MODAL -->
                <div class="modal fade" id="modalJugador" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">Registrar Jugador</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <form id="formJugador">
                                
                                <div class="modal-body">
                                    <input type="hidden" name="id" id="id">

                                    <div class="row">

                                        <div class="col-12 mb-3">
                                            <label>Nombre</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Dorsal</label>
                                            <input type="number" class="form-control" id="dorsal" name="dorsal" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Goles</label>
                                            <input type="number" class="form-control" id="goles" name="goles" required>
                                        </div>

                                        <div class="col-12 mb-3">
                                            <label>Nacionalidad</label>
                                            <input type="text" class="form-control" id="nacionalidad" name="nacionalidad" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Posición</label>
                                            <select class="form-select" id="posicion" name="posicion" required>
                                                <option value="" selected disabled>Seleccione...</option>
                                                <option>Portero</option>
                                                <option>Defensa</option>
                                                <option>Mediocampista</option>
                                                <option>Delantero</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Equipo</label>
                                            <select class="form-select" id="id_equipo" name="id_equipo" required></select>
                                        </div>

                                        <div class="col-12 mb-3">
                                            <label class="form-label">Titular</label><br>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="titular" name="titular" value="1">
                                                <label class="form-check-label">¿Es titular?</label>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>

                <?php include 'app/views/inc/footer.php'; ?>

            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <?php include 'app/views/inc/script.php'; ?>
        <script src="app/ajax/jugador.js?v=3"></script>
</body>

</html>