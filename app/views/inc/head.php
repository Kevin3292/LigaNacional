<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestion de Torneos</title>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/assets/css/bootstrap.css">

<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/assets/vendors/iconly/bold.css">

<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/assets/vendors/bootstrap-icons/bootstrap-icons.css">
<link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/assets/css/app.css">
<link rel="shortcut icon" href="<?php echo APP_URL; ?>app/views/assets/images/favicon.svg" type="image/x-icon">

<!-- Favicon: usa el logo existente para evitar 404 -->
<link rel="shortcut icon" href="<?php echo APP_URL; ?>app/views/assets/images/logo/logo6.png" type="image/png">

<style>
  /* Botón con el mismo look que .sidebar-link de Mazer */
  .sidebar .btn-as-link.sidebar-link{
    /* quita aspecto de botón nativo */
    appearance: none;
    -webkit-appearance: none;
    background: transparent;
    border: 0;
    outline: 0;
    box-shadow: none;
    -webkit-tap-highlight-color: transparent;

    /* igual que un link del sidebar */
    display: flex;
    align-items: center;
    gap: .6rem;
    width: 100%;
    padding: .6rem 1rem;
    border-radius: .35rem;      /* mismo radio que los links */
    color: inherit;
    text-align: left;
    cursor: pointer;
  }
  /* hover igual que los links */
  .sidebar .btn-as-link.sidebar-link:hover{
    color: var(--bs-primary);
    background-color: transparent; /* evita gris raro en algunos temas */
  }
  /* sin marco feo al enfocar, pero accesible */
  .sidebar .btn-as-link.sidebar-link:focus{ outline: none; box-shadow: none; }
  .sidebar .btn-as-link.sidebar-link:focus-visible{
    outline: none;
    box-shadow: inset 0 0 0 2px rgba(13,110,253,.2); /* sutil opcional */
  }

  
  .tabla-partida th, .tabla-partida td { vertical-align: middle; }
  .tabla-partida thead th { background:#f0f4f8; }
  .partida .card-header { background:#d6eadf; font-weight:600; }
  .tabla-partida .concepto { font-style: italic; color:#333; }

  #sidebar .sidebar-item.active > a.sidebar-link{
    background:#eef4ff !important;
    border-radius:10px;
  }

  /* >>> Texto/ícono NEGRO en el item activo <<< */
  #sidebar .sidebar-item.active > a.sidebar-link,
  #sidebar .sidebar-item.active > a.sidebar-link span,
  #sidebar .sidebar-item.active > a.sidebar-link i,
  #sidebar .sidebar-item.active > a.sidebar-link *{
    color:#111 !important;
  }

  /* Barra izquierda */
  #sidebar .sidebar-item{ position:relative; }
  #sidebar .sidebar-item.active::before{
    content:""; position:absolute; left:0; top:8px; bottom:8px; width:4px;
    background:#0d6efd; border-radius:0 4px 4px 0;
  }

  /* Submenú activo también en negro */
  #sidebar .submenu .submenu-item.active > a,
  #sidebar .submenu .submenu-item.active > a span,
  #sidebar .submenu .submenu-item.active > a i{
    background:#f3f7ff !important;
    color:#111 !important;
    border-radius:8px;
  }

  /* Color base de enlaces del sidebar (por si algún tema los deja muy claros) */
  #sidebar .sidebar-link{ color:#212529 !important; }
  #sidebar .sidebar-link:hover{ color:#0d6efd !important; }

</style>
