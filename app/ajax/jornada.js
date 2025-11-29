let tablaJornadas;

$(document).ready(function () {
    // 1. SEGURIDAD UI: Si no es admin, ocultamos el botón de "Generar Fixture"
    if (typeof ROL_USUARIO !== 'undefined' && ROL_USUARIO !== 'ADMINISTRADOR') {
        $(".btn-admin").hide(); 
    }
    
    cargarTablaJornadas();
});

/* ==============================
   1. CARGAR TABLA JORNADAS
================================= */
function cargarTablaJornadas() {
    tablaJornadas = $("#tablaJornadas").DataTable({
        ajax: {
            url: "app/controllers/jornadaController.php?opcion=listar_jornadas",
            dataSrc: "data"
        },
        order: [], // Respetar orden del servidor (1, 2, 3...)
        columns: [
            { data: "numero" },
            { data: "inicio" },
            { data: "fin" },
            { 
                data: "simulada",
                render: function(data) {
                    return data == 1 
                        ? '<span class="badge bg-success">Finalizada</span>' 
                        : '<span class="badge bg-secondary">Pendiente</span>';
                }
            },
            {
                data: "id",
                className: "text-center",
                render: function (data, type, row) {
                    
                    // --- SEGURIDAD UI EN FILAS ---
                    let esAdmin = (typeof ROL_USUARIO !== 'undefined' && ROL_USUARIO === 'ADMINISTRADOR');
                    let btnSimular = '';
                    
                    if (row.simulada == 1) {
                        // Ya simulada: Botón gris inactivo
                        btnSimular = `<button class="btn btn-secondary btn-sm me-1" disabled title="Ya finalizada"><i class="bi bi-check-all"></i></button>`;
                    } else {
                        // Pendiente
                        if (esAdmin) {
                            btnSimular = `<button class="btn btn-primary btn-sm me-1" onclick="simularJornada(${data}, '${row.numero}')" title="Simular Resultados"><i class="bi bi-play-fill"></i> Simular</button>`;
                        } else {
                            btnSimular = `<button class="btn btn-secondary btn-sm me-1" disabled title="Solo Administradores"><i class="bi bi-lock-fill"></i></button>`;
                        }
                    }

                    // Botón Ver Resultados (Para todos)
                    let btnVer = `<button class="btn btn-info btn-sm text-white" onclick="verResultados(${data}, '${row.numero}', ${row.simulada})" title="Ver Marcadores"><i class="bi bi-eye"></i> Resultados</button>`;

                    return btnSimular + btnVer;
                }
            }
        ],
        destroy: true,
        language: {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "Sin resultados encontrados",
            "paginate": { "first": "Primero", "last": "Ultimo", "next": "Siguiente", "previous": "Anterior" }
        }
    });
}

/* ==============================
   2. GENERAR FIXTURE (Faltaba esto)
================================= */
function confirmarGenerarFixture() {
    Swal.fire({
        title: '¿Generar Fixture?',
        text: "Se crearán las 22 jornadas automáticamente y se borrará cualquier calendario anterior de este torneo.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, generar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            generarFixture();
        }
    });
}

function generarFixture() {
    Swal.fire({ 
        title: 'Generando...', 
        text: 'Calculando cruces y fechas...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading() 
    });

    $.post("app/controllers/jornadaController.php?opcion=generar", {}, (res) => {
        // Parseo seguro
        const data = (typeof res === 'string') ? JSON.parse(res) : res;
        
        if (data.status === "success") {
            Swal.fire("¡Listo!", data.message, "success");
            tablaJornadas.ajax.reload();
        } else {
            Swal.fire("Error", data.message, "error");
        }
    });
}

/* ==============================
   3. SIMULAR JORNADA
================================= */
function simularJornada(idJornada, nombre) {
    Swal.fire({
        title: '¿Simular ' + nombre + '?',
        text: "Se generarán los marcadores aleatoriamente según la fuerza de los equipos.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, jugar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Jugando partidos...', didOpen: () => Swal.showLoading() });

            $.post("app/controllers/resultadosController.php?opcion=simular", { id_jornada: idJornada }, (res) => {
                const data = (typeof res === 'string') ? JSON.parse(res) : res;

                if (data.status === "success") {
                    Swal.fire("¡Finalizada!", data.message, "success");
                    tablaJornadas.ajax.reload();
                } else if (data.status === "warning") {
                    Swal.fire("Atención", data.message, "warning");
                } else {
                    Swal.fire("Error", data.message, "error");
                }
            });
        }
    });
}

/* ==============================
   4. VER RESULTADOS
================================= */
function verResultados(idJornada, nombreJornada, esSimulada) {
    if (esSimulada == 0) {
        Swal.fire({ icon: 'info', title: 'Pendiente', text: 'Debes SIMULAR la jornada primero para ver resultados.' });
        return; 
    }

    $("#tituloModalPartidos").text("Marcadores - " + nombreJornada);
    $("#tablaDetallePartidos tbody").html('<tr><td colspan="5" class="text-center">Cargando...</td></tr>');
    $("#modalPartidos").modal("show");

    $.ajax({
        url: "app/controllers/jornadaController.php?opcion=listar_partidos_por_jornada",
        type: "GET",
        data: { id_jornada: idJornada },
        dataType: "json",
        success: function (res) {
            let html = "";
            if (res.data && res.data.length > 0) {
                res.data.forEach(p => {
                    html += `
                        <tr class="align-middle">
                            <td><small class="text-muted">${p.fecha_hora}</small></td>
                            <td class="text-end fw-bold">
                                ${p.local} <img src="${p.img_local}" width="30" class="ms-2">
                            </td>
                            <td class="text-center bg-light">
                                <span class="badge bg-dark fs-6">${p.goleslocal} - ${p.golesvisitante}</span>
                            </td>
                            <td class="text-start fw-bold">
                                <img src="${p.img_visita}" width="30" class="me-2"> ${p.visita}
                            </td>
                            <td><small class="text-muted">${p.estadio || 'N/A'}</small></td>
                        </tr>
                    `;
                });
            } else {
                html = '<tr><td colspan="5" class="text-center">No hay datos disponibles.</td></tr>';
            }
            $("#tablaDetallePartidos tbody").html(html);
        }
    });
}