$(document).ready(function() {
    if (ROL_USUARIO !== 'ADMINISTRADOR') {
        $(".btn-admin").hide(); // O .remove() para borrarlos del DOM
    }
    console.log("Entramos");
    cargarTabla();
});

function cargarTabla() {
    $.ajax({
        url: "app/controllers/posicionesController.php?opcion=listar",
        type: "GET",
        dataType: "json",
        success: function(res) {
            let html = "";
            let data = res.data;

            if (data.length > 0) {
                data.forEach((equipo, index) => {
                    let posicion = index + 1;
                    
                    // Estilo para clasificados (Top 8)
                    let claseFila = "";
                    if (posicion <= 8) {
                        claseFila = "zona-clasificacion bg-light-success"; // Verde
                    } else {
                        claseFila = "zona-eliminado"; // Rojo/Normal
                    }

                    // Diferencia de goles (si es positiva verde, negativa roja)
                    let claseDG = equipo.DG > 0 ? 'text-success' : (equipo.DG < 0 ? 'text-danger' : '');

                    html += `
                        <tr class="${claseFila}">
                            <td class="text-center fw-bold">${posicion}</td>
                            <td class="col-club">
                                <img src="${equipo.imagen}" width="30" class="me-2 rounded-circle border">
                                <span class="fw-bold">${equipo.nombre}</span>
                            </td>
                            <td class="text-center">${equipo.PJ}</td>
                            <td class="text-center">${equipo.PG}</td>
                            <td class="text-center">${equipo.PE}</td>
                            <td class="text-center">${equipo.PP}</td>
                            <td class="text-center">${equipo.GF}</td>
                            <td class="text-center">${equipo.GC}</td>
                            <td class="text-center fw-bold ${claseDG}">${equipo.DG}</td>
                            <td class="text-center bg-light fw-bold fs-6">${equipo.PTS}</td>
                        </tr>
                    `;
                });
            } else {
                html = '<tr><td colspan="10" class="text-center py-4">Aún no hay partidos jugados en este torneo.</td></tr>';
            }

            $("#tablaPosiciones tbody").html(html);
        },
        error: function(xhr, status, error) {
            console.error("Error en AJAX:", error);
            console.log("Respuesta:", xhr.responseText);
        }
    });
}