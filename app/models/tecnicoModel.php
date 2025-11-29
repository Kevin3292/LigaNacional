<?php
require_once __DIR__ . "/../../config/conexion.php";

class TecnicoModel
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    public function listar()
    {
        $sql = "SELECT * FROM tecnico ORDER BY id DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregar($nombre, $nacionalidad)
    {
        $sql = "INSERT INTO tecnico (nombre, nacionalidad) 
                VALUES (:nombre, :nacionalidad)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":nacionalidad", $nacionalidad);
        return $stmt->execute();
    }

    public function obtener($id)
    {
        $sql = "SELECT * FROM tecnico WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function editar($id, $nombre, $nacionalidad)
    {
        $sql = "UPDATE tecnico 
                SET nombre = :nombre, nacionalidad = :nacionalidad 
                WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":nacionalidad", $nacionalidad);
        return $stmt->execute();
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM tecnico WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
