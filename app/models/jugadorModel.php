<?php
require_once __DIR__ . "/../../config/conexion.php";

class JugadorModel
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }
    
    public function listar()
    {
        $sql = "SELECT j.*, e.nombre AS equipo
                FROM jugador j
                INNER JOIN equipo e ON j.id_equipo = e.id
                ORDER BY j.id DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorEquipo($idEquipo)
    {
        $sql = "SELECT j.*, e.nombre as equipo 
                FROM jugador j
                INNER JOIN equipo e ON j.id_equipo = e.id
                WHERE j.id_equipo = :ideq";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":ideq", $idEquipo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- CORRECCIÓN AQUÍ: ORDEN DE PARÁMETROS ALINEADO ---
    public function agregar($nombre, $dorsal, $posicion, $nacionalidad, $goles, $titular, $id_equipo)
    {
        $sql = "INSERT INTO jugador (nombre, dorsal, posicion, nacionalidad, goles, titular, id_equipo)
                VALUES (:nombre, :dorsal, :posicion, :nacionalidad, :goles, :titular, :id_equipo)";
        
        $stmt = $this->conexion->prepare($sql);

        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":dorsal", $dorsal);
        $stmt->bindParam(":posicion", $posicion);
        $stmt->bindParam(":nacionalidad", $nacionalidad);
        $stmt->bindParam(":goles", $goles);
        $stmt->bindParam(":titular", $titular);
        $stmt->bindParam(":id_equipo", $id_equipo);

        return $stmt->execute();
    }

    public function obtener($id)
    {
        $sql = "SELECT * FROM jugador WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- CORRECCIÓN AQUÍ TAMBIÉN ---
    public function editar($id, $nombre, $dorsal, $posicion, $nacionalidad, $goles, $titular, $id_equipo)
    {
        $sql = "UPDATE jugador 
                SET nombre = :nombre, 
                    dorsal = :dorsal, 
                    posicion = :posicion, 
                    nacionalidad = :nacionalidad, 
                    goles = :goles, 
                    titular = :titular, 
                    id_equipo = :id_equipo
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":dorsal", $dorsal);
        $stmt->bindParam(":posicion", $posicion);
        $stmt->bindParam(":nacionalidad", $nacionalidad);
        $stmt->bindParam(":goles", $goles);
        $stmt->bindParam(":titular", $titular);
        $stmt->bindParam(":id_equipo", $id_equipo);

        return $stmt->execute();
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM jugador WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}