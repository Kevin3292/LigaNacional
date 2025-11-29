<?php
require_once __DIR__ . "/../../config/conexion.php";

class ResultadosModel
{
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    // NOTA: Eliminamos 'validarOrdenSimulacion' porque el Trigger 'validar_orden_simulacion'
    // en la base de datos ya se encarga de proteger esto.

    public function simularJornada($idJornada) {
        try {
            $this->pdo->beginTransaction();

            // 1. OBTENER PARTIDOS Y FUERZAS
            $sql = "SELECT p.id, 
                           el.fuerza as f_local, 
                           ev.fuerza as f_visita 
                    FROM partido p
                    INNER JOIN equipo el ON p.id_local = el.id
                    INNER JOIN equipo ev ON p.id_visitante = ev.id
                    WHERE p.id_jornada = :idJ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':idJ' => $idJornada]);
            $partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 2. ALGORITMO DE SIMULACIÓN DE GOLES
            $sqlUpd = "UPDATE partido SET goleslocal = :gl, golesvisitante = :gv WHERE id = :id";
            $stmtUpd = $this->pdo->prepare($sqlUpd);

            foreach ($partidos as $p) {
                $fLocal = (int)$p['f_local'];
                $fVisita = (int)$p['f_visita'];

                // Factor Localía: +5 puntos de fuerza
                $fLocal += 5;

                // Calcular Goles
                $resultado = $this->calcularGoles($fLocal, $fVisita);

                $stmtUpd->execute([
                    ':gl' => $resultado['local'],
                    ':gv' => $resultado['visita'],
                    ':id' => $p['id']
                ]);
            }

            // 3. MARCAR JORNADA COMO SIMULADA
            // AQUÍ ES DONDE EL TRIGGER ACTÚA:
            // Si intentas poner simulada=1 y la anterior está en 0, 
            // el trigger bloqueará este UPDATE y saltará al bloque 'catch' de abajo.
            $sqlJ = "UPDATE jornada SET simulada = 1 WHERE id = :id";
            $this->pdo->prepare($sqlJ)->execute([':id' => $idJornada]);

            $this->pdo->commit();
            return ["status" => "success", "message" => "Jornada simulada correctamente."];

        } catch (PDOException $e) {
            $this->pdo->rollBack();

            // --- CAPTURA DEL ERROR DEL TRIGGER ---
            // Código 45000 es el estándar para errores lanzados manualmente (SIGNAL SQLSTATE)
            if ($e->getCode() == '45000') {
                // Obtenemos el mensaje exacto que definimos en el Trigger
                $mensajeTrigger = isset($e->errorInfo[2]) ? $e->errorInfo[2] : $e->getMessage();
                
                // Retornamos 'warning' para que salga en amarillo
                return ["status" => "warning", "message" => $mensajeTrigger];
            }

            return ["status" => "error", "message" => "Error SQL: " . $e->getMessage()];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ["status" => "error", "message" => "Error Interno: " . $e->getMessage()];
        }
    }

    // Algoritmo Privado de Probabilidad
    private function calcularGoles($fuerzaA, $fuerzaB) {
        $diff = $fuerzaA - $fuerzaB;
        
        $golesA = rand(0, 2);
        $golesB = rand(0, 2);

        // Si A es mucho más fuerte
        if ($diff > 30) { 
            $golesA += rand(1, 3); 
        } elseif ($diff > 15) {
            $golesA += rand(0, 2); 
        } elseif ($diff > 5) {
            $golesA += rand(0, 1); 
        }
        
        // Si B es más fuerte
        if ($diff < -30) {
            $golesB += rand(1, 3);
        } elseif ($diff < -15) {
            $golesB += rand(0, 2);
        } elseif ($diff < -5) {
            $golesB += rand(0, 1);
        }

        return ['local' => $golesA, 'visita' => $golesB];
    }
}
?>