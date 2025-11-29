<!DOCTYPE html>
<html lang="es">

<head>
    <?php include 'app/views/inc/head.php'; ?>
</head>

<body>
    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <!-- LOGO + TEXTO A LA PAR -->
                            <div class="d-flex justify-content-center py-4">
                                <a href="#" class="logo d-flex align-items-center gap-2 w-auto text-decoration-none">
                                    <img src="<?php echo APP_URL; ?>app/views/assets/images/logo/logo6.png"
                                         width="100" height="100" alt="Logo torneo">
                                    <span class="fw-bold text-uppercase">
                                        TU SITIO DEPORTIVO FAVORITO
                                    </span>
                                </a>
                            </div>

                            <div class="card shadow-lg border-0 rounded-4 text-center mb-3">
                                <div class="card-body">

                                    <div class="pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Registro de Usuario</h5>
                                        <p class="text-center small">Cree una cuenta</p>
                                    </div>

                                    <form class="row g-3" method="post" id="formRegistro" autocomplete="off">
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input class="form-control" id="email" name="email" type="email" placeholder=" " required />
                                                <label for="email">Email</label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="input-group input-group-merge">
                                                <div class="form-floating flex-grow-1">
                                                    <input class="form-control" id="clave" name="clave" type="password"
                                                           placeholder=" " maxlength="16" minlength="8" required autocomplete="off" />
                                                    <label for="clave">Contraseña</label>
                                                </div>
                                                <button class="btn btn-outline-secondary" type="button" id="showPassword">
                                                    <i id="iconPassword" class="bi bi-eye-fill"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="d-grid mb-1 mb-2 text-center">
                                            <button class="btn btn-success" type="submit"> Registrarse </button>
                                        </div>

                                        <div class="text-center mt-2">
                                            <span class="small">¿Ya tienes cuenta?</span>
                                            <a href="login" class="small fw-semibold">Inicia sesión</a>
                                        </div>
                                    </form>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.33/dist/sweetalert2.all.min.js"></script>
    <script src="<?php echo APP_URL; ?>app/ajax/registro.js"></script>
</body>
</html>
