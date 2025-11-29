$(document).ready(function () {
  if (ROL_USUARIO !== "ADMINISTRADOR") {
    $(".btn-admin").hide(); // O .remove() para borrarlos del DOM
  }

  cargarEquipos();
  cargarSelects();

  $("#formEquipo").submit(function (e) {
    e.preventDefault();
    GuardarYEditarEquipo(this);
  });
});

function cargarSelects() {
  $.getJSON("app/controllers/tecnicoController.php?opcion=listar", (res) => {
    res.data.forEach((t) => {
      $("#id_tecnico").append(new Option(t.nombre, t.id));
    });
  });

  $.getJSON("app/controllers/estadioController.php?opcion=listar", (res) => {
    res.data.forEach((es) => {
      $("#id_estadio").append(new Option(es.nombre, es.id));
    });
  });
}

function GuardarYEditarEquipo(form) {
  const formData = new FormData(form);

  const id = formData.get("id");
  const opcion = id ? "editar" : "agregar";

  $.ajax({
    url: "app/controllers/equipoController.php?opcion=" + opcion,
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (res) {
      if (res.status === "success") {
        mostrarToast("success", res.message);
        form.reset();
        recargarEquipos();
        $("#modalEquipo").modal("hide");
      } else {
        mostrarError(res.message);
      }
    },
  });
}

function editar(id) {
  $.getJSON(
    "app/controllers/equipoController.php?opcion=obtener&id=" + id,
    (res) => {
      const e = res.data;

      $("#id").val(e.id);
      $("#nombre").val(e.nombre);
      $("#ciudad").val(e.ciudad);
      $("#fuerza").val(e.fuerza);
      $("#id_estadio").val(e.id_estadio);
      $("#id_tecnico").val(e.id_tecnico);

      $("#preview").attr("src", e.imagen);

      $("#modalEquipo").modal("show");
    }
  );
}

function eliminar(id) {
  Swal.fire({
    title: "¿Eliminar equipo?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Eliminar",
  }).then((r) => {
    if (r.isConfirmed) {
      $.post(
        "app/controllers/equipoController.php?opcion=eliminar",
        { id },
        (res) => {
          const data = JSON.parse(res);
          if (data.status === "success") {
            mostrarToast("success", "Equipo eliminado");
            recargarEquipos();
          }
        }
      );
    }
  });
}

function cargarEquipos() {
    tabla = $("#tablaEquipos").DataTable({
        ajax: {
            url: "app/controllers/equipoController.php?opcion=listar",
            dataSrc: "data",
        },
        columns: [
            // 1. ID (Oculto)
            { data: "id", visible: false },

            // 2. Logo
            {
                data: "imagen",
                render: (img) => `<img src="${img}" class="rounded" width="50">`,
            },
            
            // 3. Nombre
            { data: "nombre", className: "fw-bold" },
            
            // 4. Ciudad
            { data: "ciudad" },
            
            // 5. Estadio (Movido antes de Técnico según tu HTML)
            { data: "estadio" },
            
            // 6. Técnico
            { data: "tecnico" },

            // 7. ESTADO (Nueva Columna)
            {
                data: "estado",
                className: "text-center",
                render: function (data) {
                    // data viene como 1 (Activo) o 0 (Inactivo)
                    if (data == 1) {
                        return '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Activo</span>';
                    } else {
                        return '<span class="badge bg-secondary"><i class="bi bi-exclamation-circle"></i> Incompleto</span>';
                    }
                }
            },

            // 8. ACCIONES
            {
                data: null,
                className: "text-center notexport",
                orderable: false,
                render: function (data, type, row) {
                    // Lógica de seguridad (Rol)
                    // Verifica que ROL_USUARIO esté definido, si no, asume cliente por seguridad
                    let esAdmin = (typeof ROL_USUARIO !== "undefined" && ROL_USUARIO === "ADMINISTRADOR");
                    
                    let estadoBtn = esAdmin ? "" : "disabled";
                    let titulo = esAdmin ? "Acciones" : "Solo Lectura";
                    
                    // Opcional: quitamos onclick si no es admin
                    let accionEditar = esAdmin ? `onclick="editar(${row.id})"` : '';
                    let accionEliminar = esAdmin ? `onclick="eliminar(${row.id})"` : '';

                    return `
                    <div class="dropdown">
                      <button class="btn btn-light btn-sm border-0 shadow-sm rounded-circle" 
                              data-bs-toggle="dropdown" 
                              ${estadoBtn} 
                              title="${titulo}">
                        <i class="bi bi-three-dots-vertical"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end shadow-sm py-2">
                        <li>
                            <button class="dropdown-item d-flex align-items-center gap-2" ${accionEditar}>
                                <i class="bi bi-pencil-square text-primary"></i> Editar
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item d-flex align-items-center gap-2" ${accionEliminar}>
                                <i class="bi bi-trash text-danger"></i> Eliminar
                            </button>
                        </li>
                      </ul>
                    </div>`;
                },
            },
        ],
        // Configuraciones extra recomendadas
        responsive: true,
        language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json" },
        dom: "<'row align-items-center mb-2'<'col-md-4'l><'col-md-4'f><'col-md-4 text-end'B>>" +
             "<'row'<'col-12'tr>>" +
             "<'row mt-2'<'col-md-5'i><'col-md-7'p>>",
        buttons: [
            { extend: "excelHtml5", className: "btn btn-success btn-sm", text: '<i class="bi bi-file-excel"></i> Excel' },
            { extend: "pdfHtml5", className: "btn btn-danger btn-sm", text: '<i class="bi bi-file-pdf"></i> PDF' }
        ]
    });
}

function recargarEquipos() {
  $("#tablaEquipos").DataTable().ajax.reload(null, false);
}

function mostrarError(msg) {
  Swal.fire("Error", msg, "error");
}

function mostrarToast(icon, msg) {
  Swal.fire({
    toast: true,
    icon,
    title: msg,
    position: "top-end",
    timer: 2000,
    showConfirmButton: false,
  });
}


