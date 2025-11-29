<!DOCTYPE html>
<html lang="es">

<head>
    <?php include 'app/views/inc/head.php'; ?>
</head>

<body>
    <main>

        <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-light">
            <div class="card shadow-lg border-0 rounded-4 text-center p-4" style="max-width: 420px; width: 100%;">

                <div class="d-flex justify-content-center mb-3">
                    <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center shadow"
                        style="width: 60px; height: 60px;">
                        <i class="bi bi-box-arrow-right fs-2"></i>
                    </div>
                </div>

                <h4 class="fw-semibold mb-2">Confirmar cierre en la contabilidad de esta empresa</h4>
                <hr>
                <p class="fs-5 text-muted">
                    Actualmente tienes una sesión activa en la empresa <strong class="text-dark"><?php echo $_SESSION['empresa']['nombre']; ?></strong>.
                    ¿Deseas cerrar la contabilidad por ahora?
                </p>

                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 mt-4">
                    <a href="contabilidad" class="btn btn-secondary px-4">
                        Cancelar
                    </a>
                    <button onclick="confirmCierre();" class="btn btn-primary bg-gradient px-4">
                        Cerrar sesión
                    </button>
                </div>
            </div>
        </div>
    </main>
    <script src="<?php echo APP_URL; ?>app/ajax/cierre.js"></script>
</body>

</html>