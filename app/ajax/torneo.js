$(document).ready(function () {
  if (ROL_USUARIO !== 'ADMINISTRADOR') {
        $(".btn-admin").hide(); // O .remove() para borrarlos del DOM
    }
  cargarTorneos();

  $("#formTorneo").submit(function (e) {
    e.preventDefault();
    GuardarYEditar(this); // le paso el formulario
  });
});

function GuardarYEditar(form) {
  const formData = new FormData(form);

  // Leer el id del hidden
  const id = formData.get("idtorneo"); // "" si es nuevo, o el id si estás editando
  const opcion = id && id !== "0" ? "editar" : "registrar";

  $.ajax({
    url: "app/controllers/torneoController.php?opcion=" + opcion,
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        form.reset();
        $("#idtorneo").val(""); // limpiar id para volver a modo registrar

        mostrarToast(
          "success",
          response.message ||
            (opcion === "registrar"
              ? "Torneo registrado exitosamente."
              : "Torneo actualizado exitosamente.")
        );

        recargarTorneos();
        $("#modalEmpresa").modal("hide");
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

function editar(idtorneo) {
  $.ajax({
    url: "app/controllers/torneoController.php?opcion=mostrar",
    type: "GET",
    dataType: "json",
    data: { id: idtorneo },
    success: function (data) {
      if (data.status === "success") {
        const torneo = data.data;

        console.log(torneo.nombre);
        $("#idtorneo").val(torneo.id);
        $("#nombre").val(torneo.nombre);
        $("#fecha_inicio").val(torneo.fechainicio); // o torneo.inicio si así lo manda PHP
        $("#fecha_fin").val(torneo.fechafin); // igual aquí

        // Si tienes un select para el estado:
        // $("#estado").val(torneo.estado);

        $("#modalEmpresa").modal("show");
      } else {
        mostrarError(data.message || "No se pudo cargar el torneo.");
      }
    },
    error: function (xhr) {
      console.error(xhr.responseText);
      mostrarError("Error al cargar el torneo.");
    },
  });
}

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

let tablaTorneos = null;

function cargarTorneos() {
  if (tablaTorneos !== null) {
    return; // ya está inicializada
  }

  tablaTorneos = $("#tablaTorneos").DataTable({
    ajax: {
      url: "app/controllers/torneoController.php?opcion=traerTorneos",
      type: "get",
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
      { data: "nombre", defaultContent: "" },
      { data: "inicio", defaultContent: "" },
      { data: "fin", defaultContent: "" },
      {
        data: "estado",
        className: "text-center",
        render: function (data) {
          const texto = data == 1 ? "ACTIVO" : "INACTIVO";
          const clase = data == 1 ? "bg-success" : "bg-secondary";
          return `<span class="badge ${clase}">${texto}</span>`;
        },
      },
      {
        data: null,
        className: "text-center notexport",
        orderable: false,
        searchable: false,
        render: (row) => {
          
          // 1. VERIFICAR ROL
          const esAdmin = (typeof ROL_USUARIO !== 'undefined' && ROL_USUARIO === 'ADMINISTRADOR');

          // 2. CONSTRUIR HTML DINÁMICO
          // Si es admin, mostramos Editar y Eliminar. Si no, cadena vacía.
          let menuAdmin = '';
          
          if (esAdmin) {
            menuAdmin = `
              <li>
                <button class="dropdown-item d-flex align-items-center gap-2"
                        onclick="editar(${row.id})">
                  <i class="bi bi-pencil-square text-primary"></i>
                  <span>Editar</span>
                </button>
              </li>

              <li>
                <button class="dropdown-item d-flex align-items-center gap-2"
                        onclick="eliminar(${row.id})">
                  <i class="bi bi-trash3 text-danger"></i>
                  <span>Eliminar</span>
                </button>
              </li>
              
              <li><hr class="dropdown-divider"></li>
            `;
          }

          // 3. RETORNAR MENÚ COMPLETO
          // Nota: El botón "Ver Torneo" siempre está disponible para todos
          return `
          <div class="dropdown">
            <button class="btn btn-light btn-sm border-0 shadow-sm rounded-circle"
                    data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-expanded="false" title="Acciones">
              <i class="bi bi-three-dots-vertical"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow-sm py-2">
              <li>
                <h6 class="dropdown-header text-secondary d-flex align-items-center gap-2">
                  <i class="bi bi-tools"></i>
                  <span>Acciones</span>
                </h6>
              </li>

              ${menuAdmin} <li>
                <button class="dropdown-item d-flex align-items-center gap-2"
                        onclick="seleccionarTorneo(${row.id})">
                  <i class="bi bi-trophy text-success"></i>
                  <span>Ver Torneo</span>
                </button>
              </li>
            </ul>
          </div>
          `;
        },
      },
    ],
    // ... resto de tu configuración DOM y Buttons ...
    columnDefs: [
      { responsivePriority: 1, targets: 0 },
      { responsivePriority: 2, targets: 1 },
    ],
    buttons: [
      {
        extend: "pdfHtml5",
        text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
        className: "btn btn-outline-danger",
        title: "Torneos",
        messageTop: "Listado de torneos",
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
function recargarTorneos() {
  if (tablaTorneos) {
    tablaTorneos.ajax.reload(null, false); // false = mantener paginación
  }
}

function seleccionarTorneo(idtorneo) {
  $.ajax({
    url: "app/controllers/torneoController.php?opcion=seleccionar",
    type: "POST",
    dataType: "json",
    data: { id: idtorneo },
    success: function (response) {
      console.log("respuesta seleccionar:", response); // DEBUG
      if (response.status === "success") {
        window.location.href = "iniciotorneo";
      } else {
        mostrarError(response.message || "No se pudo seleccionar el torneo.");
      }
    },
    error: function (xhr) {
      console.error(xhr.responseText);
      mostrarError("Error en el servidor al seleccionar el torneo.");
    },
  });
}


