$("#formRegistro").on("submit", function (e) {
    e.preventDefault();
    var formData = new FormData($("#formRegistro")[0]);

    $.ajax({
        url: "app/controllers/usuarioController.php?opcion=registrar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                Swal.fire({
                    title: "¡Registrado!",
                    text: response.message || "Usuario registrado correctamente",
                    icon: "success",
                    confirmButtonText: "Ir a iniciar sesión",
                    allowOutsideClick: false
                }).then(() => {
                    location.href = "login";
                });
            } else {
                mostrarError(response.message || "No se pudo registrar el usuario");
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            mostrarError("Error en el servidor. Intente más tarde.");
        }
    });
});

$("#showPassword").on("click", function () {
    const $input = $("#clave");
    const $icon  = $("#iconPassword");

    if ($input.attr("type") === "password") {
        $input.attr("type", "text");
        $icon.removeClass("bi-eye-fill").addClass("bi-eye-slash-fill");
    } else {
        $input.attr("type", "password");
        $icon.removeClass("bi-eye-slash-fill").addClass("bi-eye-fill");
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
