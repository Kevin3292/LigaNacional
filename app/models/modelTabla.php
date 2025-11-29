<?php
require_once __DIR__ . "/../../config/conexion.php";

class PosicionesModel
{
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    public function obtenerTabla($torneoId)
    {
        // NOTA: No usamos 'registrotabla', calculamos directo de los 'partidos'
        // para asegurar que siempre coincida con los resultados reales.
        
        $sql = "SELECT 
                    e.id, 
                    e.nombre, 
                    e.imagen,
                    
                    -- Partidos Jugados (Solo si la jornada ya fue simulada)
                    SUM(CASE WHEN j.simulada = 1 THEN 1 ELSE 0 END) as PJ,
                    
                    -- Partidos Ganados
                    SUM(CASE 
                        WHEN j.simulada = 1 AND (
                            (p.id_local = e.id AND p.goleslocal > p.golesvisitante) OR
                            (p.id_visitante = e.id AND p.golesvisitante > p.goleslocal)
                        ) THEN 1 ELSE 0 
                    END) as PG,

                    -- Partidos Empatados
                    SUM(CASE 
                        WHEN j.simulada = 1 AND p.goleslocal = p.golesvisitante THEN 1 ELSE 0 
                    END) as PE,

                    -- Partidos Perdidos
                    SUM(CASE 
                        WHEN j.simulada = 1 AND (
                            (p.id_local = e.id AND p.goleslocal < p.golesvisitante) OR
                            (p.id_visitante = e.id AND p.golesvisitante < p.goleslocal)
                        ) THEN 1 ELSE 0 
                    END) as PP,

                    -- Goles a Favor
                    COALESCE(SUM(CASE 
                        WHEN j.simulada = 1 AND p.id_local = e.id THEN p.goleslocal 
                        WHEN j.simulada = 1 AND p.id_visitante = e.id THEN p.golesvisitante 
                        ELSE 0 
                    END), 0) as GF,

                    -- Goles en Contra
                    COALESCE(SUM(CASE 
                        WHEN j.simulada = 1 AND p.id_local = e.id THEN p.golesvisitante 
                        WHEN j.simulada = 1 AND p.id_visitante = e.id THEN p.goleslocal 
                        ELSE 0 
                    END), 0) as GC,

                    -- Puntos (3 por victoria, 1 por empate)
                    (
                        (SUM(CASE 
                            WHEN j.simulada = 1 AND (
                                (p.id_local = e.id AND p.goleslocal > p.golesvisitante) OR
                                (p.id_visitante = e.id AND p.golesvisitante > p.goleslocal)
                            ) THEN 1 ELSE 0 
                        END) * 3) +
                        (SUM(CASE WHEN j.simulada = 1 AND p.goleslocal = p.golesvisitante THEN 1 ELSE 0 END))
                    ) as PTS

                FROM equipo e
                
                -- 1. Unimos con la tabla intermedia para filtrar por torneo
                INNER JOIN torneo_equipo te ON e.id = te.equipo_id
                
                -- 2. Buscamos partidos donde el equipo sea Local O Visitante
                LEFT JOIN partido p ON (e.id = p.id_local OR e.id = p.id_visitante)
                
                -- 3. Unimos con jornada para verificar si está 'simulada'
                LEFT JOIN jornada j ON p.id_jornada = j.id
                
                WHERE te.torneo_id = :tid
                
                GROUP BY e.id, e.nombre, e.imagen
                
                -- Orden: Puntos > Diferencia Goles > Goles Favor > Nombre
                ORDER BY PTS DESC, e.nombre ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':tid' => $torneoId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}