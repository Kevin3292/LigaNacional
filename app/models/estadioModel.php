<?php
require_once __DIR__ . "/../../config/conexion.php";

class EstadioModel
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    public function listar()
    {
        $sql = "SELECT * FROM estadio ORDER BY id DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregar($nombre, $ubicacion, $capacidad)
    {
        $sql = "INSERT INTO estadio (nombre, ubicacion, capacidad) VALUES (:nombre, :ubicacion, :capacidad)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":ubicacion", $ubicacion);
        $stmt->bindParam(":capacidad", $capacidad);
        return $stmt->execute();
    }

    public function obtener($id)
    {
        $sql = "SELECT * FROM estadio WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function editar($id, $nombre, $ubicacion, $capacidad)
    {
        $sql = "UPDATE estadio SET nombre = :nombre, ubicacion = :ubicacion, capacidad = :capacidad WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":ubicacion", $ubicacion);
        $stmt->bindParam(":capacidad", $capacidad);
        return $stmt->execute();
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM estadio WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
