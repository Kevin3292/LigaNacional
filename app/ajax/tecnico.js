$(document).ready(function () {
  // 1. SEGURIDAD: Ocultar botón de "Nuevo Técnico" si no es Admin
  // Asegúrate de que tu botón de crear tenga la clase .btn-admin o .btn-outline-success
  if (typeof ROL_USUARIO !== 'undefined' && ROL_USUARIO !== 'ADMINISTRADOR') {
    // Si usas la clase generica btn-admin:
    $(".btn-admin").hide(); 
    // Si no le has puesto clase btn-admin, oculta por el estilo del boton:
    $(".btn-outline-success").hide();
  }

  cargarTecnicos();

  $("#formTecnico").submit(function (e) {
    e.preventDefault();
    GuardarYEditarTecnico(this);
  });
});

/* ==============================
   GUARDAR / EDITAR
================================= */
function GuardarYEditarTecnico(form) {
  const formData = new FormData(form);
  const id = formData.get("id");
  const opcion = id && id !== "0" ? "editar" : "agregar";

  $.ajax({
    url: "app/controllers/tecnicoController.php?opcion=" + opcion,
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        form.reset();
        $("#id").val("");
        mostrarToast("success", response.message);
        recargarTecnicos();
        $("#modalTecnico").modal("hide");
      } else {
        mostrarError(response.message);
      }
    },
    error: function (xhr) {
      console.error(xhr.responseText);
      mostrarError("Error en el servidor.");
    },
  });
}

/* ==============================
   CARGAR DATOS PARA EDITAR
================================= */
function editar(id) {
  $.ajax({
    url: "app/controllers/tecnicoController.php?opcion=obtener",
    type: "GET",
    data: { id },
    dataType: "json",
    success: function (data) {
      if (data.status === "success") {
        const t = data.data;
        $("#id").val(t.id);
        $("#nombre").val(t.nombre);
        $("#nacionalidad").val(t.nacionalidad);
        $("#modalTecnico").modal("show");
      }
    },
    error: function () {
      mostrarError("Error al cargar técnico.");
    },
  });
}

/* ==============================
   ELIMINAR
================================= */
function eliminar(id) {
  Swal.fire({
    title: "¿Eliminar Técnico?",
    text: "Esta acción no puede deshacerse.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Eliminar",
    cancelButtonText: "Cancelar",
  }).then((r) => {
    if (r.isConfirmed) {
      $.ajax({
        url: "app/controllers/tecnicoController.php?opcion=eliminar",
        type: "POST",
        data: { id },
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            mostrarToast("success", "Técnico eliminado.");
            recargarTecnicos();
          } else {
            mostrarError("No se pudo eliminar");
          }
        },
      });
    }
  });
}

/* ==============================
   TOASTS Y ERRORES
================================= */
function mostrarError(msg) {
  Swal.fire({ icon: "error", title: "ERROR", text: msg });
}

function mostrarToast(icono, msg) {
  const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
  });
  Toast.fire({ icon: icono, title: msg });
}

/* ==============================
   DATATABLE
================================= */
let tablaTecnicos = null;

function cargarTecnicos() {
  if (tablaTecnicos !== null) return;

  tablaTecnicos = $("#tablaTecnicos").DataTable({
    ajax: {
      url: "app/controllers/tecnicoController.php?opcion=listar",
      type: "GET",
      dataType: "json",
      dataSrc: "data",
    },
    language: { url: "app/ajax/idioma.json" },
    aaSorting: [],
    pageLength: 5,
    responsive: true,
    columns: [
      { data: "id", visible: false },
      { data: "nombre", render: (d) => `<strong>${d}</strong>` },
      { data: "nacionalidad" },
      {
        data: null,
        className: "text-center notexport",
        orderable: false,
        // Usamos la función auxiliar modificada
        render: (row) => accionesHTML(row),
      },
    ],
    // ... resto de tu config DOM y Buttons ...
    dom: "<'row align-items-center mb-2'<'col-md-4'l><'col-md-4'f><'col-md-4 text-end'B>>" +
         "<'row'<'col-12'tr>>" +
         "<'row mt-2'<'col-md-5'i><'col-md-7'p>>",
    buttons: [
      {
        extend: "pdfHtml5",
        text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
        className: "btn btn-outline-danger",
        title: "Técnicos",
        messageTop: "Listado de técnicos",
        exportOptions: { columns: ":not(.notexport)" },
        download: "open",
      },
      {
        extend: "excelHtml5",
        text: '<i class="bi bi-file-earmark-excel"></i> Excel',
        className: "btn btn-outline-success",
        exportOptions: { columns: ":not(.notexport)" },
      },
      {
        extend: "print",
        text: '<i class="bi bi-printer"></i> Imprimir',
        className: "btn btn-outline-primary",
        exportOptions: { columns: ":not(.notexport)" },
      },
    ],
  });
}

/* ==============================
   FUNCIÓN DE ACCIONES (MODIFICADA)
================================= */
function accionesHTML(row) {
  
  // 1. VALIDACIÓN DE ROL
  const esAdmin = (typeof ROL_USUARIO !== 'undefined' && ROL_USUARIO === 'ADMINISTRADOR');
  
  // 2. ESTADO VISUAL
  // Si no es admin, agregamos 'disabled' al botón principal
  const estadoBtn = esAdmin ? '' : 'disabled';
  const titulo = esAdmin ? 'Acciones' : 'Solo Lectura';

  // 3. PROTECCIÓN DE ONCLICK (Opcional pero recomendada)
  const eventoEditar = esAdmin ? `onclick="editar(${row.id})"` : '';
  const eventoEliminar = esAdmin ? `onclick="eliminar(${row.id})"` : '';

  return `
    <div class="dropdown">
      <button class="btn btn-light btn-sm border-0 shadow-sm rounded-circle"
              data-bs-toggle="dropdown"
              title="${titulo}"
              ${estadoBtn}> <!-- AQUI SE APLICA EL BLOQUEO -->
        <i class="bi bi-three-dots-vertical"></i>
      </button>

      <ul class="dropdown-menu dropdown-menu-end shadow-sm py-2">
        <li>
          <button class="dropdown-item d-flex align-items-center gap-2"
                  ${eventoEditar}>
            <i class="bi bi-pencil-square text-primary"></i> Editar
          </button>
        </li>

        <li>
          <button class="dropdown-item d-flex align-items-center gap-2"
                  ${eventoEliminar}>
            <i class="bi bi-trash text-danger"></i> Eliminar
          </button>
        </li>
      </ul>
    </div>
  `;
}

function recargarTecnicos() {
  if (tablaTecnicos) tablaTecnicos.ajax.reload(null, false);
}