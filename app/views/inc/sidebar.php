<?php
// Asegúrate de tener la sesión iniciada ANTES de este include
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Usuario y rol
$usuario   = $_SESSION['usuario'] ?? null;
$usuarioRol = $usuario['rol'] ?? 'cliente';   // 'admin' o 'cliente'
$isAdmin    = ($usuarioRol === 'Administrador');

// Torneo seleccionado (cuando el usuario hace "Ver Torneo")
$torneoNombre = $_SESSION['torneo']['nombre'] ?? '';
$torneoId     = $_SESSION['torneo']['id']     ?? null;

// Ruta actual (primer segmento de la URL amigable)
$uriPath  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$current  = trim($uriPath, '/'); // ej: "torneos", "equipos", "torneo-inicio", ""
if ($current === '') $current = 'inicio';     // página raíz = inicio

// Helper para marcar activo
$active = function ($targets) use ($current) {
    $targets = (array)$targets;
    return in_array($current, $targets, true) ? 'active' : '';
};

// Menú de módulos dentro de un torneo
$isMenuTorneo = in_array($current, [
    'torneo-inicio',
    'equipos',
    'tecnicos',
    'estadios',
    'jugadores',
    'jornadas',
    'partidos',
    'tablaposiciones'
], true);
?>

<style>
  .sidebar-menu .menu .sidebar-item.active > a.sidebar-link{
    background:#eaf3ff;
    color:#0d6efd;
    border-radius:10px;
  }
  .sidebar-menu .menu .sidebar-item.active > a.sidebar-link i{
    color:inherit;
  }
  .sidebar-menu .menu .sidebar-item{ position:relative; }
  .sidebar-menu .menu .sidebar-item.active::before{
    content:"";
    position:absolute;
    left:0;
    top:8px;
    bottom:8px;
    width:4px;
    background:#0d6efd;
    border-radius:0 4px 4px 0;
  }
</style>

<div id="sidebar" class="active">
  <div class="sidebar-wrapper active">

    <div class="sidebar-header">
      <div class="d-flex justify-content-between">
        <div class="logo">
          <a href="torneos">
            <img src="<?php echo APP_URL; ?>app/views/assets/images/logo/logo.png" alt="Logo">
          </a>
        </div>
        <div class="toggler">
          <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
        </div>
      </div>
    </div>

    <div class="sidebar-menu">
      <ul class="menu">
        <li class="sidebar-title">Menú</li>

        <?php if(!$torneoId):?>
        <!-- Torneos (siempre visible: admin y cliente) -->
        <li class="sidebar-item <?= $active(['torneos']) ?>">
          <a href="torneos" class="sidebar-link" aria-current="<?= $active(['torneos']) ? 'page' : 'false' ?>">
            <i class="bi bi-trophy-fill"></i>
            <span>Torneos</span>
          </a>
        </li>
          <?php endif ?>

        <?php if ($torneoId): ?>
          <li class="sidebar-title mt-3">
            Torneo actual: <strong><?= htmlspecialchars($torneoNombre) ?></strong>
          </li>

          <!-- Inicio del torneo (dashboard) -->
          <li class="sidebar-item <?= $active('torneo-inicio') ?>">
            <a href="iniciotorneo" class="sidebar-link">
              <i class="bi bi-speedometer2"></i>
              <span>Inicio del Torneo</span>
            </a>
          </li>

          <!-- Equipos -->
          <li class="sidebar-item <?= $active('equipos') ?>">
            <a href="equipos" class="sidebar-link">
              <i class="bi bi-people-fill"></i>
              <span>Equipos</span>
            </a>
          </li>

          <!-- Técnicos -->
          <li class="sidebar-item <?= $active('tecnicos') ?>">
            <a href="tecnicos" class="sidebar-link">
              <i class="bi bi-person-badge-fill"></i>
              <span>Técnicos</span>
            </a>
          </li>

          <!-- Estadios -->
          <li class="sidebar-item <?= $active('estadios') ?>">
            <a href="estadios" class="sidebar-link">
              <i class="bi bi-building"></i>
              <span>Estadios</span>
            </a>
          </li>

          <!-- Jugadores -->
          <li class="sidebar-item <?= $active('jugadores') ?>">
            <a href="jugadores" class="sidebar-link">
              <i class="bi bi-person-lines-fill"></i>
              <span>Jugadores</span>
            </a>
          </li>

          <!-- Jornadas -->
          <li class="sidebar-item <?= $active('jornadas') ?>">
            <a href="jornadas" class="sidebar-link">
              <i class="bi bi-calendar-week"></i>
              <span>Jornadas</span>
            </a>
          </li>

          <!-- Tabla de posiciones -->
          <li class="sidebar-item <?= $active('tablaposiciones') ?>">
            <a href="tabla" class="sidebar-link">
              <i class="bi bi-list-ol"></i>
              <span>Tabla de Posiciones</span>
            </a>
          </li>

          <!-- Salir del torneo actual -->
          <li class="sidebar-item">
            <button type="button" class="btn-as-link sidebar-link" id="btnSalirTorneo">
              <i class="bi bi-arrow-left-circle"></i>
              <span>Salir del torneo</span>
            </button>
          </li>
        <?php else: ?>
          <li class="sidebar-item">
            <div class="sidebar-link text-muted">
              <i class="bi bi-info-circle"></i>
              <span>Selecciona un torneo para ver sus opciones</span>
            </div>
          </li>
        <?php endif; ?>
      </ul>
    </div>

    <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
  </div>
</div>
