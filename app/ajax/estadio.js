$(document).ready(function () {
  if (ROL_USUARIO !== "ADMINISTRADOR") {
    $(".btn-admin").hide(); // O .remove() para borrarlos del DOM
  }

  cargarEstadios();

  $("#formEstadio").submit(function (e) {
    e.preventDefault();
    GuardarYEditarEstadio(this); // le paso el formulario
  });
});

/* ============================
   GUARDAR / EDITAR
=============================== */
function GuardarYEditarEstadio(form) {
  const formData = new FormData(form);

  const id = formData.get("id"); // "" si es nuevo
  const opcion = id && id !== "0" ? "editar" : "agregar";

  $.ajax({
    url: "app/controllers/estadioController.php?opcion=" + opcion,
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        form.reset();
        $("#id").val("");

        mostrarToast(
          "success",
          response.message ||
            (opcion === "agregar"
              ? "Estadio registrado exitosamente."
              : "Estadio actualizado exitosamente.")
        );

        recargarEstadios();
        $("#modalEstadio").modal("hide");
      } else {
        mostrarError(response.message || "Ocurrió un error.");
      }
    },
    error: function (xhr) {
      console.error(xhr.responseText);
      mostrarError("Error en el servidor. Intente más tarde.");
    },
  });
}

/* ============================
   CARGAR DATOS PARA EDITAR
=============================== */
function editar(id) {
  $.ajax({
    url: "app/controllers/estadioController.php?opcion=obtener",
    type: "GET",
    dataType: "json",
    data: { id: id },
    success: function (data) {
      if (data.status === "success") {
        const e = data.data;

        $("#id").val(e.id);
        $("#nombre").val(e.nombre);
        $("#ubicacion").val(e.ubicacion);
        $("#capacidad").val(e.capacidad);

        $("#modalEstadio").modal("show");
      } else {
        mostrarError(data.message || "No se pudo cargar el estadio.");
      }
    },
    error: function (xhr) {
      console.error(xhr.responseText);
      mostrarError("Error al cargar el estadio.");
    },
  });
}

/* ============================
   ELIMINAR
=============================== */
function eliminar(id) {
  Swal.fire({
    title: "¿Eliminar estadio?",
    text: "Esta acción no se puede deshacer",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((r) => {
    if (r.isConfirmed) {
      $.ajax({
        url: "app/controllers/estadioController.php?opcion=eliminar",
        type: "POST",
        data: { id: id },
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            mostrarToast("success", "Estadio eliminado correctamente.");
            recargarEstadios();
          } else {
            mostrarError("No se pudo eliminar el estadio.");
          }
        },
        error: function (xhr) {
          console.error(xhr.responseText);
          mostrarError("Error al eliminar el estadio.");
        },
      });
    }
  });
}

/* ============================
   TOAST / ERROR
=============================== */
function mostrarError(mensaje) {
  Swal.fire({
    title: "ERROR",
    text: mensaje,
    icon: "error",
    confirmButtonText: "Aceptar",
    allowOutsideClick: false,
  });
}

function mostrarToast(icono, mensaje) {
  const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
  });
  Toast.fire({ icon: icono, title: mensaje });
}

/* ============================
   DATATABLE
=============================== */
let tablaEstadios = null;
function cargarEstadios() {
  if (tablaEstadios !== null) return;

  tablaEstadios = $("#tablaEstadios").DataTable({
    ajax: {
      url: "app/controllers/estadioController.php?opcion=listar",
      type: "GET",
      dataType: "json",
      dataSrc: "data",
    },
    language: { url: "app/ajax/idioma.json" },
    aaSorting: [],
    pageLength: 5,
    lengthMenu: [
      [5, 12, 18, -1],
      [5, 12, 18, "Todos"],
    ],
    responsive: true,
    columns: [
      { data: "id", visible: false, searchable: false },
      { data: "nombre" },
      { data: "ubicacion" },
      { data: "capacidad" },
      {
        data: null,
        className: "text-center notexport",
        orderable: false,
        render: (row) => {
          
          // 1. LÓGICA DE SEGURIDAD (ROL)
          // Verificamos si es admin. Si no, deshabilitamos el botón.
          const esAdmin = (typeof ROL_USUARIO !== 'undefined' && ROL_USUARIO === 'admin');
          const estadoBtn = esAdmin ? '' : 'disabled';
          const titulo = esAdmin ? 'Acciones' : 'Solo Lectura';
          
          // Opcional: Quitamos el onclick si no es admin para doble seguridad visual
          const accionEditar = esAdmin ? `onclick="editar(${row.id})"` : '';
          const accionEliminar = esAdmin ? `onclick="eliminar(${row.id})"` : '';

          return `
          <div class="dropdown">
            <button class="btn btn-light btn-sm border-0 shadow-sm rounded-circle"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    aria-expanded="false"
                    title="${titulo}"
                    ${estadoBtn}>  <i class="bi bi-three-dots-vertical"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow-sm py-2">

              <li>
                <h6 class="dropdown-header text-secondary d-flex align-items-center gap-2">
                  <i class="bi bi-tools"></i> <span>Acciones</span>
                </h6>
              </li>

              <li>
                <button class="dropdown-item d-flex align-items-center gap-2"
                        ${accionEditar}>
                  <i class="bi bi-pencil-square text-primary"></i>
                  <span>Editar</span>
                </button>
              </li>

              <li>
                <button class="dropdown-item d-flex align-items-center gap-2"
                        ${accionEliminar}>
                  <i class="bi bi-trash3 text-danger"></i>
                  <span>Eliminar</span>
                </button>
              </li>

            </ul>
          </div>
        `;
        },
      },
    ],

    columnDefs: [
      { responsivePriority: 1, targets: 0 },
      { responsivePriority: 2, targets: 1 },
    ],

    buttons: [
      {
        extend: "pdfHtml5",
        text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
        className: "btn btn-outline-danger",
        title: "Estadios",
        messageTop: "Listado de estadios",
        exportOptions: { columns: ":not(.notexport)" },
        download: "open",
      },
      {
        extend: "excelHtml5",
        text: '<i class="bi bi-file-earmark-excel"></i> Excel',
        className: "btn btn-outline-success",
        title: "Estadios",
        messageTop: "Listado de estadios",
        exportOptions: { columns: ":not(.notexport)" },
      },
      {
        extend: "print",
        text: '<i class="bi bi-printer"></i> Imprimir',
        className: "btn btn-outline-primary",
        title: "Estadios",
        messageTop: "Listado de estadios",
        exportOptions: { columns: ":not(.notexport)" },
      },
    ],

    dom:
      "<'row align-items-center mb-2'\
          <'col-12 col-md-4 d-flex align-items-center'l>\
          <'col-12 col-md-4 mt-2 mt-md-0 d-flex justify-content-md-center'f>\
          <'col-12 col-md-4 mt-2 mt-md-0 text-md-end'B>\
        >" +
      "<'row'<'col-12'tr>>" +
      "<'row mt-2'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
  });
}

function recargarEstadios() {
  if (tablaEstadios) {
    tablaEstadios.ajax.reload(null, false);
  }
}
