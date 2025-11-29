<?php
require_once __DIR__ . "/../../config/conexion.php";

class EquipoModel
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    // Ahora pedimos el ID del torneo para no mezclar equipos de otros años
    public function listarPorTorneo($idTorneo)
    {
        $sql = "SELECT e.id, e.nombre, e.ciudad, e.estado, e.imagen,
                       t.nombre AS tecnico,
                       es.nombre AS estadio
                FROM equipo e
                -- Aquí hacemos la magia: Unimos con la tabla intermedia
                INNER JOIN torneo_equipo te ON e.id = te.equipo_id
                LEFT JOIN tecnico t ON e.id_tecnico = t.id
                LEFT JOIN estadio es ON e.id_estadio = es.id
                -- Y filtramos solo los de ESTE torneo
                WHERE te.torneo_id = :id_torneo
                ORDER BY e.nombre ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_torneo", $idTorneo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregar($nombre, $ciudad, $fuerza, $idEstadio, $idTecnico, $imagen, $idTorneo)
    {
        try {
            // ---------------------------------------------------------
            // PASO 1: INICIAR TRANSACCIÓN
            // ---------------------------------------------------------
            $this->conexion->beginTransaction();

            // ---------------------------------------------------------
            // PASO 2: INSERTAR EL EQUIPO (Tabla: equipo)
            // ---------------------------------------------------------
            $sql = "INSERT INTO equipo (nombre, ciudad, fuerza, id_estadio, id_tecnico, imagen, estado)
                    VALUES (:nombre, :ciudad, :fuerza, :id_estadio, :id_tecnico, :imagen, 0)";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":ciudad", $ciudad);
            $stmt->bindParam(":fuerza", $fuerza);
            $stmt->bindParam(":id_estadio", $idEstadio);
            $stmt->bindParam(":id_tecnico", $idTecnico);
            $stmt->bindParam(":imagen", $imagen, PDO::PARAM_LOB);

            if (!$stmt->execute()) {
                throw new Exception("Error al guardar el equipo.");
            }

            $idEquipoNuevo = $this->conexion->lastInsertId();

            // ---------------------------------------------------------
            // PASO 3: RELACIONARLO CON EL TORNEO (Tabla: torneo_equipo)
            // ---------------------------------------------------------
            $sqlRelacion = "INSERT INTO torneo_equipo (torneo_id, equipo_id) 
                        VALUES (:torneo_id, :equipo_id)";

            $stmtRel = $this->conexion->prepare($sqlRelacion);
            $stmtRel->bindParam(':torneo_id', $idTorneo);
            $stmtRel->bindParam(':equipo_id', $idEquipoNuevo);

            if (!$stmtRel->execute()) {
                throw new Exception("Error al vincular el equipo al torneo.");
            }

            // ---------------------------------------------------------
            // PASO 4: CONFIRMAR TRANSACCIÓN
            // ---------------------------------------------------------
            $this->conexion->commit();

            return ["status" => "success", "message" => "Equipo agregado exitosamente."];
        } catch (PDOException $e) {
            // =========================================================
            // CAPTURA DE ERRORES DE BASE DE DATOS (TRIGGERS)
            // =========================================================

            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            if ($e->getCode() == '45000') {
                $mensajeRaw = $e->getMessage();

                $pos = strrpos($mensajeRaw, ": ");

                if ($pos !== false) {
                    $mensajeLimpio = substr($mensajeRaw, $pos + 2);
                } else {
                    $mensajeLimpio = $mensajeRaw;
                }

                $mensajeLimpio = str_replace("1644 ", "", $mensajeLimpio);

                return ["status" => "error", "message" => $mensajeLimpio];
            }

            // Otros errores
            return ["status" => "error", "message" => "Error de base de datos: " . $e->getMessage()];
        } catch (Exception $e) {
            // Si algo falla, revertimos TODO (Rollback)
            // Verificamos si hay transacción activa antes de hacer rollback
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            return ["status" => "error", "message" => "Error: " . $e->getMessage()];
        }
    }



    public function obtener($id)
    {
        $sql = "SELECT * FROM equipo WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function editar($id, $nombre, $ciudad, $fuerza, $idEstadio, $idTecnico, $imagen)
    {
        try {
            // A. Construcción dinámica del SQL
            // Si $imagen es NULL, NO tocamos la columna 'imagen' para no borrar la foto vieja.
            if ($imagen !== null) {
                $sql = "UPDATE equipo 
                        SET nombre = :nombre, ciudad = :ciudad, fuerza = :fuerza,
                            id_estadio = :id_estadio, id_tecnico = :id_tecnico,
                            imagen = :imagen
                        WHERE id = :id";
            } else {
                // Versión SIN actualizar imagen
                $sql = "UPDATE equipo 
                        SET nombre = :nombre, ciudad = :ciudad, fuerza = :fuerza,
                            id_estadio = :id_estadio, id_tecnico = :id_tecnico
                        WHERE id = :id";
            }

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":ciudad", $ciudad);
            $stmt->bindParam(":fuerza", $fuerza);
            $stmt->bindParam(":id_estadio", $idEstadio);
            $stmt->bindParam(":id_tecnico", $idTecnico);

            // Solo hacemos bind de imagen si no es null
            if ($imagen !== null) {
                $stmt->bindParam(":imagen", $imagen, PDO::PARAM_LOB);
            }

            // B. Ejecutar (Aquí saltará el Trigger si hay error)
            $stmt->execute();

            return ["status" => "success", "message" => "Equipo actualizado correctamente."];

        } catch (PDOException $e) {
            // C. Captura del Trigger (Tu código mágico ✨)
            if ($e->getCode() == '45000') {
                $mensajeRaw = $e->getMessage();
                $pos = strrpos($mensajeRaw, ": ");
                
                $mensajeLimpio = ($pos !== false) ? substr($mensajeRaw, $pos + 2) : $mensajeRaw;
                $mensajeLimpio = str_replace("1644 ", "", $mensajeLimpio);

                return ["status" => "error", "message" => $mensajeLimpio];
            }

            return ["status" => "error", "message" => "Error BD: " . $e->getMessage()];

        } catch (Exception $e) {
            return ["status" => "error", "message" => "Error: " . $e->getMessage()];
        }
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM equipo WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
