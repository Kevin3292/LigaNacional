<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'app/views/inc/head.php'; ?>
    <style>
        .table-posiciones thead th {
            background-color: #2c3e50;
            color: white;
            text-align: center;
        }
        .col-club { text-align: left !important; width: 40%; }
        .col-pts { font-weight: bold; background-color: #f8f9fa; }
        .zona-clasificacion { border-left: 5px solid #2ecc71; } /* Borde verde para clasificados */
        .zona-eliminado { border-left: 5px solid #e74c3c; }
    </style>
</head>
<body data-page="posiciones">
    <div id="app">
        <?php include 'app/views/inc/sidebar.php'; ?>
        <div id="main">
            <?php include 'app/views/inc/header.php'; ?>

            <div class="page-heading">
                <h3>Tabla General</h3>
                <p class="text-muted">Posiciones del Torneo Actual.</p>
            </div>

            <div class="page-content">
                <section class="section">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-posiciones" id="tablaPosiciones">
                                    <thead>
                                        <tr>
                                            <th>Pos</th>
                                            <th class="col-club">Club</th>
                                            <th>PJ</th>
                                            <th>PG</th>
                                            <th>PE</th>
                                            <th>PP</th>
                                            <th>GF</th>
                                            <th>GC</th>
                                            <th>DG</th>
                                            <th class="col-pts">PTS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <small><span class="badge bg-success">&nbsp;</span> Clasifica a Cuartos de Final (Top 8)</small>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <?php include 'app/views/inc/footer.php'; ?>
        </div>
    </div>

    <?php include 'app/views/inc/script.php'; ?>
    <script src="app/ajax/posiciones.js"></script>
</body>
</html>