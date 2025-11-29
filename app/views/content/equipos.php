<!DOCTYPE html>
<html lang="es">

<head>
    <?php include 'app/views/inc/head.php'; ?>
</head>

<body data-page="equipos">
<div id="app">

<?php include 'app/views/inc/sidebar.php'; ?>
<div id="main">
<?php include 'app/views/inc/header.php'; ?>

<div class="page-heading">
    <h3>Gestión de Equipos</h3>
    <p class="text-muted">Administra los equipos registrados.</p>
</div>

<div class="page-content">
<section class="section">
<div class="card">
<div class="card-header d-flex justify-content-between align-items-center">
    <span>EQUIPOS</span>
    <button class="btn btn-outline-success btn-admin" data-bs-toggle="modal" data-bs-target="#modalEquipo">
        + Nuevo Equipo
    </button>

    
</div>
<div class="card-body">
<table class="table table-striped w-100" id="tablaEquipos">
<thead>
<tr>
    <th style="display:none;">ID</th>
    <th>Logo</th>
    <th>Nombre</th>
    <th>Ciudad</th>
    <th>Estadio</th>
    <th>Técnico</th>
    <th>Estado</th>
    <th class="notexport text-center">Acciones</th>
</tr>
</thead>
<tbody></tbody>
</table>
</div>
</div>
</section>

<!-- MODAL -->
<div class="modal fade" id="modalEquipo" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header">
    <h5 class="modal-title">Registrar Equipo</h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form id="formEquipo" enctype="multipart/form-data">

<div class="modal-body">

<input type="hidden" id="id" name="id">

<div class="mb-3">
<label>Nombre</label>
<input type="text" class="form-control" name="nombre" id="nombre" required>
</div>

<div class="mb-3">
<label>Ciudad</label>
<input type="text" class="form-control" name="ciudad" id="ciudad" required>
</div>

<div class="mb-3">
<label>Fuerza</label>
<input type="number" class="form-control" name="fuerza" id="fuerza" required>
</div>

<div class="mb-3">
<label>Estadio</label>
<select class="form-select" name="id_estadio" id="id_estadio" required></select>
</div>

<div class="mb-3">
<label>Técnico</label>
<select class="form-select" name="id_tecnico" id="id_tecnico" required></select>
</div>

<div class="mb-3">
<label>Imagen (Obligatoria)</label>
<input type="file" class="form-control" name="imagen" id="imagen" accept="image/*" required>
<img id="preview" class="mt-2 rounded" width="120">
</div>

</div>

<div class="modal-footer">
<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
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
<script src="app/ajax/equipo.js?v=4"></script>

</body>
</html>
