$("#btnCerrarSesion").click(function() {
     window.setTimeout(function() {
        Swal.fire({
            title: '¿Desea cerrar sesión?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                confirmCierre();
            }
        });
    }, 130);
});

function confirmCierre(){
    $.ajax({
    url: "app/controllers/empresaController.php?opcion=cerrarContabilidad",
    type: "POST",
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
    },
  });
}