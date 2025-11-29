let tablaJugadores; 

$(document).ready(function () {
    // 1. SEGURIDAD: Ocultar botones si no es Admin
    if (typeof ROL_USUARIO !== 'undefined' && ROL_USUARIO !== 'ADMINISTRADOR') {
        $(".btn-admin").hide(); 
        $(".btn-outline-success").hide(); 
    }

    // 2. Cargar los Selects (Filtro y Modal)
    cargarSelectEquipos();

    // 3. Cargar la Tabla (Inicialmente muestra todos)
    cargarJugadores();

    // 4. EVENTO: Cuando cambian el filtro, recargamos la tabla
    $("#filtroEquipo").change(function () {
        // DataTables detectará el cambio al recargar porque lee el valor en la config 'data'
        tablaJugadores.ajax.reload();
    });

    // 5. Formulario Guardar
    $("#formJugador").submit(function (e) {
        e.preventDefault();
        GuardarYEditarJugador(this);
    });
});

/* ==============================
   CARGAR SELECTS DE EQUIPOS
================================= */
function cargarSelectEquipos() {
    // Llenamos tanto el filtro de arriba como el select del modal
    $.getJSON("app/controllers/equipoController.php?opcion=listar", (res) => {
        if (res.data) {
            // Limpiamos y ponemos opción default
            $("#filtroEquipo").empty().append('<option value="">Ver Todos los Equipos</option>');
            $("#id_equipo").empty().append('<option value="" selected disabled>Seleccione...</option>');

            res.data.forEach(eq => {
                // Select del Filtro
                $("#filtroEquipo").append(new Option(eq.nombre, eq.id));
                // Select del Modal
                $("#id_equipo").append(new Option(eq.nombre, eq.id));
            });
        }
    });
}

/* ==============================
   CARGAR TABLA (DataTables)
================================= */
function cargarJugadores() {
    tablaJugadores = $("#tablaJugadores").DataTable({
        ajax: {
            url: "app/controllers/jugadorController.php?opcion=listar",
            type: "GET",
            // AQUÍ ESTÁ LA CLAVE: Enviamos el valor del select al PHP
            data: function (d) {
                d.id_equipo = $("#filtroEquipo").val(); 
            },
            dataSrc: "data"
        },
        language: { url: "app/ajax/idioma.json" }, // O tu URL al CDN si prefieres
        responsive: true,
        order: [],
        columns: [
            { data: "id", visible: false },
            { data: "nombre", render: (d) => `<strong>${d}</strong>` },
            { data: "dorsal", className: "text-center" },
            { data: "posicion" },
            { data: "equipo" }, // Este campo viene de tu SQL (e.nombre as equipo)
            { data: "goles", className: "text-center" },
            {
                data: "titular",
                className: "text-center",
                render: t => t == 1 
                    ? `<span class="badge bg-success">Titular</span>`
                    : `<span class="badge bg-secondary">Suplente</span>`
            },
            {
                // COLUMNA ACCIONES
                data: null,
                className: "text-center notexport",
                orderable: false,
                render: function (data, type, row) {
                    
                    let esAdmin = (typeof ROL_USUARIO !== 'undefined' && ROL_USUARIO === 'ADMINISTRADOR');
                    let estadoBtn = esAdmin ? '' : 'disabled';
                    let titulo = esAdmin ? 'Acciones' : 'Solo Lectura';
                    
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
                }
            }
        ],
        // Opcional: Botones de exportación
        dom: "<'row align-items-center mb-2'<'col-md-4'l><'col-md-4'f><'col-md-4 text-end'B>>" +
             "<'row'<'col-12'tr>>" +
             "<'row mt-2'<'col-md-5'i><'col-md-7'p>>",
        buttons: [
            { extend: "excelHtml5", className: "btn btn-success btn-sm", text: "Excel" },
            { extend: "pdfHtml5", className: "btn btn-danger btn-sm", text: "PDF" }
        ]
    });
}
/* ==============================
   GUARDAR / EDITAR
================================= */
function GuardarYEditarJugador(form) {
    const formData = new FormData(form);
    const id = formData.get("id");
    const opcion = id ? "editar" : "agregar";

    $.ajax({
        url: "app/controllers/jugadorController.php?opcion=" + opcion,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (res) {
            if (res.status === "success") {
                mostrarToast("success", res.message);
                form.reset();
                $("#id").val("");
                $("#modalJugador").modal("hide");
                
                // Recargamos la tabla (manteniendo el filtro si está seleccionado)
                tablaJugadores.ajax.reload(null, false);
            } else {
                mostrarError(res.message);
            }
        }
    });
}

/* ==============================
   EDITAR (Cargar datos)
================================= */
function editar(id) {
    $.getJSON("app/controllers/jugadorController.php?opcion=obtener&id=" + id, (res) => {
        if (res.status === 'success') {
            const j = res.data;
            $("#id").val(j.id);
            $("#dorsal").val(j.dorsal);
            $("#goles").val(j.goles);
            $("#nacionalidad").val(j.nacionalidad);
            $("#nombre").val(j.nombre);
            $("#posicion").val(j.posicion);
            $("#id_equipo").val(j.id_equipo);
            $("#titular").prop("checked", j.titular == 1);
            $("#modalJugador").modal("show");
        }
    });
}

/* ==============================
   ELIMINAR
================================= */
function eliminar(id) {
    Swal.fire({
        title: "¿Eliminar jugador?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar"
    }).then(r => {
        if (r.isConfirmed) {
            $.post("app/controllers/jugadorController.php?opcion=eliminar", { id }, (res) => {
                const data = (typeof res === 'string') ? JSON.parse(res) : res;
                if (data.status === "success") {
                    mostrarToast("success", "Jugador eliminado");
                    tablaJugadores.ajax.reload(null, false);
                } else {
                    mostrarError(data.message);
                }
            });
        }
    });
}

// Utilidades
function mostrarError(msg) { Swal.fire("Error", msg, "error"); }
function mostrarToast(icon, msg) {
    const Toast = Swal.mixin({ toast: true, position: "top-end", showConfirmButton: false, timer: 3000 });
    Toast.fire({ icon: icon, title: msg });
}