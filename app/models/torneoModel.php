<?php

declare(strict_types=1);
require_once __DIR__ . "/../../config/conexion.php";

class Torneo
{
    private PDO $conexion;

    // Constantes para fácil configuración
    private const JORNADAS_NECESARIAS = 22;
    private const DIAS_HOLGURA_REQUERIDOS = 14; // 2 semanas extra para liguilla/lluvia

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    public function registrarTorneo(string $nombre, string $fecha_inicio, string $fecha_fin): array
    {
        // 1. VALIDACIÓN PHP (La de las 22 jornadas sigue aquí)
        // Esta función ya la tenías, la mantenemos porque es compleja para SQL
        $validacionDuracion = $this->validarExtensionTorneo($fecha_inicio, $fecha_fin);
        if (!$validacionDuracion['es_valido']) {
            return ["status" => "error", "message" => $validacionDuracion['mensaje']];
        }

        // 2. INTENTO DE INSERTAR (Aquí saltarán los Triggers)
        try {
            $sql = "INSERT INTO torneo (nombre, fechainicio, fechafin, estado) 
                    VALUES (:nombre, :fecha_inicio, :fecha_fin, 0)"; // Estado 0 por defecto
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);
            
            $stmt->execute();
            
            return ["status" => "success", "message" => "Torneo registrado exitosamente"];

        } catch (PDOException $e) {
            
            // --- AQUÍ ATRAPAMOS EL MENSAJE DEL TRIGGER ---
            
            // Verificamos si el error viene de nuestro SIGNAL SQLSTATE '45000'
            if ($e->getCode() == '45000') {
                // $e->getMessage() trae algo como: "SQLSTATE[45000]: ... Error: Las fechas coinciden..."
                // Limpiamos un poco el mensaje para que se vea bonito
                $mensajeError = $e->errorInfo[2]; // Esto obtiene solo el texto del MESSAGE_TEXT
                
                return ["status" => "error", "message" => $mensajeError];
            }

            // Errores genéricos (conexión, sintaxis, etc)
            error_log("Error BD: " . $e->getMessage());
            return ["status" => "error", "message" => "Error interno al guardar el torneo."];
        }
    }

    // --- MÉTODOS DE LECTURA ---
    
    public function obtenerTorneos(): array
    {
        try {
            $sql = "SELECT id, nombre, estado, fechainicio AS inicio, fechafin AS fin FROM torneo ORDER BY fechainicio DESC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            return [];
        }
    }
    
    public function obtenerTorneo($id)
    {
        $sql = "SELECT * FROM torneo WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function editarTorneo(int $id, string $nombre, string $fecha_inicio, string $fecha_fin, int $estado): array 
    {
        // 1. Validación PHP de duración (22 jornadas)
        $validacion = $this->validarExtensionTorneo($fecha_inicio, $fecha_fin);
        if (!$validacion['es_valido']) {
            return ["status" => "error", "message" => "No se pudo editar: " . $validacion['mensaje']];
        }

        try {
            // 2. Intentamos Actualizar (Aquí salta el Trigger BEFORE UPDATE)
            $sql = "UPDATE torneo SET 
                        nombre = :nombre, 
                        fechainicio = :fecha_inicio, 
                        fechafin = :fecha_fin,
                        estado = :estado 
                    WHERE id = :id";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                return ["status" => "success", "message" => "Torneo actualizado correctamente."];
            }
            
            return ["status" => "error", "message" => "No se realizaron cambios."];
            
        } catch (PDOException $e) {
            
            // --- ATRAPAR ERROR DEL TRIGGER ---
            if ($e->getCode() == '45000') {
                $mensajeError = $e->errorInfo[2];
                return ["status" => "error", "message" => $mensajeError];
            }

            error_log("Error editar torneo: " . $e->getMessage());
            return ["status" => "error", "message" => "Error interno de base de datos"];
        }
    }
    
    private function validarExtensionTorneo(string $inicio, string $fin): array
    {
        $fechaInicio = new DateTime($inicio);
        $fechaFin = new DateTime($fin);

        $slotsJuego = 0;
        $cursor = clone $fechaInicio;

        // Iteramos día por día
        while ($cursor <= $fechaFin) {
            $diaSemana = $cursor->format('N'); // 1 (Lunes) a 7 (Domingo)

            // Lógica: 
            // Slot Fin de Semana: Sábado (6) o Domingo (7). 
            // Usamos Domingo como "contador" del fin de semana para no duplicar.
            if ($diaSemana == 7) { 
                $slotsJuego++; 
            }
            
            // Slot Entre Semana: Miércoles (3) o Jueves (4).
            // Usamos Miércoles como "contador" de la jornada entre semana.
            // Si el torneo empieza un Jueves, perdemos esa jornada (es aceptable).
            elseif ($diaSemana == 3) {
                $slotsJuego++;
            }

            $cursor->modify('+1 day');
        }

        // Validación final
        // Necesitamos 22 jornadas para la fase regular
        if ($slotsJuego < self::JORNADAS_NECESARIAS) {
            $faltan = self::JORNADAS_NECESARIAS - $slotsJuego;
            return [
                "es_valido" => false, 
                "mensaje" => "Rango insuficiente. Solo caben $slotsJuego jornadas (Fines de semana + Miércoles/Jueves). Faltan fechas para $faltan jornadas."
            ];
        }

        // Validación de Holgura (Liguilla)
        // Necesitamos espacio extra después de la jornada 22
        // Estimamos calculando días totales vs días necesarios
        // Esto es opcional, pero recomendado para la LMF
        $diasTotales = $fechaInicio->diff($fechaFin)->days;
        // 22 jornadas * 3.5 días promedio entre juegos = 77 días mínimos de juego puro
        if ($diasTotales < (77 + self::DIAS_HOLGURA_REQUERIDOS)) {
             return [
                "es_valido" => true, // Lo dejamos pasar pero con advertencia
                "mensaje" => "Torneo aprobado, pero el calendario estará MUY apretado para la liguilla."
            ];
        }

        return ["es_valido" => true, "mensaje" => "Calendario válido."];
    }
}