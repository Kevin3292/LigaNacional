<header class="mb-3 d-flex justify-content-between align-items-center">
    <!-- Botón del menú lateral -->
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>

    <!-- Área derecha: perfil de usuario -->
    <div class="d-flex align-items-center ms-auto">
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none"
               id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                
                <!-- Avatar (puede ser imagen o ícono) -->
                <div class="avatar avatar-md">
                    <i class="bi bi-person-circle fs-3"></i>
                </div>
                
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
                <li>
                    <h6 class="dropdown-header">Hola, Nombre!</h6>
                </li>
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="bi bi-person me-2"></i> <?php echo $_SESSION['usuario']['rol'] ?>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="bi bi-gear me-2"></i> Configuración
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <!-- AQUÍ CAMBIAMOS -->
                    <a class="dropdown-item text-danger" href="#" id="btnCerrarSesionPerfil">
                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
