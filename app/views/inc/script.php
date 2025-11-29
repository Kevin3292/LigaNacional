<!-- jQuery (una sola vez) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap 5 bundle -->
<script src="<?php echo APP_URL; ?>app/views/assets/js/bootstrap.bundle.min.js"></script>

<!-- DataTables núcleo + Bootstrap 5 + Responsive -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<!-- Buttons + Bootstrap 5 + exportadores -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>


<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<!-- ApexCharts SOLO si hay dashboard (evita errores de "Element not found") -->
<script src="<?php echo APP_URL; ?>app/views/assets/vendors/apexcharts/apexcharts.js"></script>
<script src="<?php echo APP_URL; ?>app/views/assets/js/pages/dashboard.js"></script>

<!-- Tu JS general -->
<script src="<?php echo APP_URL; ?>app/views/assets/js/main.js"></script>

<script>
  const ROL_USUARIO = "<?php echo isset($_SESSION['usuario']['rol']) ? $_SESSION['usuario']['rol'] : 'CLIENTE'; ?>";
  (function() {
    // Normaliza rutas tipo /app/registropartidas/ -> registropartidas
    const norm = s => (s || '').replace(location.origin, '')
      .replace(/^[\/]+/, '').replace(/[\/]+$/, '');
    // Toma la última parte del path como "página"
    let here = norm(location.pathname).split('/').pop() || 'index.html';

    // Si usas URLs limpias (sin .html/.php) puedes mapear así:
    // const map = { '': 'index.html', 'inicio': 'index.html' };
    // here = map[here] || here;

    let found = false;

    // 1) Links de primer nivel
    document.querySelectorAll('.sidebar-menu .sidebar-item > a.sidebar-link').forEach(a => {
      const href = norm(a.getAttribute('href'));
      if (!href || href === '#') return;
      if (href === here) {
        const li = a.closest('.sidebar-item');
        li?.classList.add('active');
        found = true;
      }
    });

    // 2) Submenús
    document.querySelectorAll('.submenu .submenu-item > a').forEach(a => {
      const href = norm(a.getAttribute('href'));
      if (!href || href === '#') return;
      if (href === here) {
        const subLi = a.closest('.submenu-item');
        const parent = a.closest('.sidebar-item.has-sub');
        const submenu = parent?.querySelector('.submenu');
        subLi?.classList.add('active');
        parent?.classList.add('active'); // marca el padre
        if (submenu) submenu.style.display = 'block'; // lo deja abierto
        found = true;
      }
    });

    // fallback: si no encontró nada, deja "Dashboard" activo
    if (!found) {
      document.querySelector('.sidebar-item > a[href="index.html"]')
        ?.closest('.sidebar-item')?.classList.add('active');
    }
  })();

  document.addEventListener('DOMContentLoaded', function() {

    const btnCerrarSesionPerfil = document.getElementById('btnCerrarSesionPerfil');

    if (btnCerrarSesionPerfil) {
      btnCerrarSesionPerfil.addEventListener('click', function(e) {
        e.preventDefault();

        // Opcional: confirmación con SweetAlert
        Swal.fire({
          title: '¿Cerrar sesión?',
          text: 'Se cerrará tu sesión actual.',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Sí, salir',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (!result.isConfirmed) return;

          // Petición al controlador de usuario
          $.ajax({
            url: "<?php echo APP_URL; ?>app/controllers/usuarioController.php?opcion=cerrar",
            type: "POST",
            dataType: "json",
            success: function(response) {
              if (response.status === "success") {
                // Redirigir al login (según tu router es 'login', no login.php)
                window.location.href = "<?php echo APP_URL; ?>login";
              } else {
                Swal.fire('Error', response.message || 'No se pudo cerrar la sesión.', 'error');
              }
            },
            error: function(xhr) {
              console.error(xhr.responseText);
              Swal.fire('Error', 'Error en el servidor al cerrar la sesión.', 'error');
            }
          });
        });
      });
    }

    const btnSalirTorneo = document.getElementById('btnSalirTorneo');
    if (btnSalirTorneo) {
      btnSalirTorneo.addEventListener('click', function(e) {
        e.preventDefault();

        Swal.fire({
          title: '¿Salir del torneo?',
          text: 'Volverás a la lista de torneos.',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Sí, salir',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (!result.isConfirmed) return;

          $.ajax({
            url: "<?php echo APP_URL; ?>app/controllers/torneoController.php?opcion=salir",
            type: "POST",
            dataType: "json",
            success: function(response) {
              if (response.status === "success") {
                // Te manda a la lista de torneos (inicio)
                window.location.href = "<?php echo APP_URL; ?>inicio";
              } else {
                Swal.fire('Error', response.message || 'No se pudo salir del torneo.', 'error');
              }
            },
            error: function(xhr) {
              console.error(xhr.responseText);
              Swal.fire('Error', 'Error en el servidor al salir del torneo.', 'error');
            }
          });
        });
      });
    }


  });
</script>