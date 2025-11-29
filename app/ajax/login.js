$("#formLogin").on("submit", function (e) {
    e.preventDefault();
    var formData = new FormData($("#formLogin")[0]);

    $.ajax({
        url: "app/controllers/usuarioController.php?opcion=ingresar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                location.href = "inicio";
            } else {
                mostrarError(response.message);
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            mostrarError("Error en el servidor. Intente más tarde.");
        }
    });
});

$("#showPassword").on("click", function () {
    if($("#clave").attr("type") === "password"){
        $("#clave").attr("type", "text");
        $("#iconPassword").removeClass("ri-eye-fill");
        $("#iconPassword").addClass("ri-eye-off-fill");
    } else {
        $("#clave").attr("type", "password");
        $("#iconPassword").removeClass("ri-eye-off-fill");
        $("#iconPassword").addClass("ri-eye-fill");
    }
});

function mostrarError(mensaje) {
    Swal.fire({
        title: "ERROR",
        text: mensaje,
        icon: "error",
        confirmButtonText: 'Aceptar',
        allowOutsideClick: false
    });
}
