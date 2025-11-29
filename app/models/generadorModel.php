<?php
require_once __DIR__ . "/../../config/conexion.php";

class FixtureModel
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Conexion::conectar();
    }

    // ====================================================================
    // 1. GENERAR EL CALENDARIO (FIXTURE)
    // ====================================================================
    public function generarFixture($torneoId, $fechaInicioStr)
    {
        try {
            // A. OBTENER EQUIPOS Y ESTADIOS
            $sql = "SELECT e.id, e.id_estadio FROM equipo e 
                    INNER JOIN torneo_equipo te ON e.id = te.equipo_id 
                    WHERE te.torneo_id = :tid";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':tid' => $torneoId]);

            $equiposData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            $idsEquipos = array_keys($equiposData);
            $numEquipos = count($idsEquipos);

            // --- VALIDACIÓN CRÍTICA: SI NO SON 12, NO HACEMOS NADA ---
            if ($numEquipos == 0) {
                return ["status" => "error", "message" => "No hay equipos inscritos en el torneo."];
            }
            if ($numEquipos != 12) {
                return ["status" => "error", "message" => "El reglamento exige exactamente 12 equipos para iniciar (Actuales: $numEquipos)."];
            }
            // ---------------------------------------------------------

            // B. INICIAR TRANSACCIÓN
            $this->pdo->beginTransaction();

            // C. LIMPIEZA PREVIA (Borrar fixture anterior si existe)
            $sqlDelP = "DELETE p FROM partido p INNER JOIN jornada j ON p.id_jornada = j.id WHERE j.torneo_id = :tid";
            $this->pdo->prepare($sqlDelP)->execute([':tid' => $torneoId]);
            $sqlDelJ = "DELETE FROM jornada WHERE torneo_id = :tid";
            $this->pdo->prepare($sqlDelJ)->execute([':tid' => $torneoId]);

            // D. ALGORITMO DE BERGER
            shuffle($idsEquipos); // Mezclar para sorteo aleatorio
            $totalJornadas = ($numEquipos - 1) * 2; // 22 Jornadas
            $mitad = $numEquipos / 2; // 6 partidos por jornada

            // Lógica de Fechas
            $fechaBase = new DateTime($fechaInicioStr);

            // BUCLE DE LAS 22 JORNADAS
            for ($j = 0; $j < $totalJornadas; $j++) {
                $numeroJornada = $j + 1;

                // 1. Determinar los dos días de juego (Sáb/Dom o Mié/Jue)
                $diaSemana = $fechaBase->format('w'); // 0=Dom, 3=Mié...
                $fechaDia1 = clone $fechaBase;
                $fechaDia2 = clone $fechaBase;

                if ($diaSemana == 0 || $diaSemana == 6) {
                    // Fin de Semana
                    if ($diaSemana == 0) { // Base es Domingo
                        $fechaDia1->modify('-1 day'); // Día 1: Sábado
                        // Día 2: Domingo (igual)
                    } else { // Base es Sábado
                        $fechaDia2->modify('+1 day'); // Día 2: Domingo
                    }
                } else {
                    // Entre Semana
                    $fechaDia2->modify('+1 day'); // Día 2: Jueves
                }

                // 2. Insertar Jornada
                // Nota: El trigger 'validar_equipos_antes_fixture' saltará aquí si algo anda mal en la BD
                $sqlJornada = "INSERT INTO jornada (numero, num, simulada, inicio, fin, torneo_id) 
                               VALUES (:numeroTxt, :numInt, 0, :inicio, :fin, :tid)";
                $stmtJ = $this->pdo->prepare($sqlJornada);
                $stmtJ->execute([
                    ':numeroTxt' => "Jornada " . $numeroJornada,
                    ':numInt'    => $numeroJornada,
                    ':inicio'    => $fechaDia1->format('Y-m-d'),
                    ':fin'       => $fechaDia2->format('Y-m-d'),
                    ':tid'       => $torneoId
                ]);
                $idJornadaCreada = $this->pdo->lastInsertId();

                // 3. Calcular Cruces (Berger)
                $esVuelta = ($j >= ($numEquipos - 1));
                $idxRonda = $j % ($numEquipos - 1);

                for ($i = 0; $i < $mitad; $i++) {
                    // Repartir partidos en los dos días
                    if ($i < 3) $fechaPartido = $fechaDia1; // Primeros 3 partidos
                    else $fechaPartido = $fechaDia2;        // Últimos 3 partidos

                    $t1 = $idsEquipos[$i];
                    $t2 = $idsEquipos[$numEquipos - 1 - $i];

                    // Lógica localía
                    if ($i == 0 && $idxRonda % 2 == 0) {
                        $local = $t2;
                        $visita = $t1;
                    } else {
                        $local = $t1;
                        $visita = $t2;
                    }

                    if ($idxRonda % 2 == 1) {
                        $temp = $local;
                        $local = $visita;
                        $visita = $temp;
                    }
                    if ($esVuelta) {
                        $temp = $local;
                        $local = $visita;
                        $visita = $temp;
                    }

                    // Insertar Partido
                    $idEstadioLocal = $equiposData[$local] ?? null;
                    $sqlPartido = "INSERT INTO partido (fecha, goleslocal, golesvisitante, id_estadio, id_jornada, id_local, id_visitante) 
                                   VALUES (:fecha, 0, 0, :estadio, :id_jornada, :local, :visita)";
                    $stmtP = $this->pdo->prepare($sqlPartido);
                    $stmtP->execute([
                        ':fecha'      => $fechaPartido->format('Y-m-d 15:00:00'),
                        ':estadio'    => $idEstadioLocal,
                        ':id_jornada' => $idJornadaCreada,
                        ':local'      => $local,
                        ':visita'     => $visita
                    ]);
                }

                // 4. Rotar equipos para la siguiente jornada
                $fixed = array_shift($idsEquipos);
                $last = array_pop($idsEquipos);
                array_unshift($idsEquipos, $last);
                array_unshift($idsEquipos, $fixed);

                // 5. Calcular fecha siguiente jornada (Saltar de Dom->Mié o Mié->Dom)
                $diaBase = $fechaBase->format('w');
                if ($diaBase == 0) $fechaBase->modify('+3 days'); // Dom -> Mié
                elseif ($diaBase == 3) $fechaBase->modify('+4 days'); // Mié -> Dom
                else {
                    if ($diaBase == 6) $fechaBase->modify('+4 days');
                    else $fechaBase->modify('+1 week');
                }
            }

            $this->pdo->commit();
            return ["status" => "success", "message" => "Fixture de 22 jornadas generado correctamente."];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            // Capturar error de Trigger (45000)
            if ($e->getCode() == '45000') {
                $msg = isset($e->errorInfo[2]) ? $e->errorInfo[2] : $e->getMessage();
                return ["status" => "error", "message" => $msg];
            }
            return ["status" => "error", "message" => "Error BD: " . $e->getMessage()];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ["status" => "error", "message" => "Error Gral: " . $e->getMessage()];
        }
    }

    // ====================================================================
    // 2. LISTAR JORNADAS (Para la tabla principal)
    // ====================================================================
    public function listarJornadas($torneoId)
    {
        $sql = "SELECT id, numero, inicio, fin, simulada 
                FROM jornada 
                WHERE torneo_id = :tid 
                ORDER BY num ASC"; // Importante ordenar por num para que salga 1,2,3...

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':tid' => $torneoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // ====================================================================
    public function obtenerPartidosPorJornada($idJornada)
    {
        $sql = "SELECT p.id, p.fecha, p.goleslocal, p.golesvisitante,
                       el.nombre as local, el.imagen as img_local,
                       ev.nombre as visita, ev.imagen as img_visita,
                       es.nombre as estadio
                FROM partido p
                INNER JOIN equipo el ON p.id_local = el.id
                INNER JOIN equipo ev ON p.id_visitante = ev.id
                LEFT JOIN estadio es ON p.id_estadio = es.id
                WHERE p.id_jornada = :idJ
                ORDER BY p.fecha ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idJ' => $idJornada]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
