<?php

declare(strict_types=1);

require_once __DIR__ . "/../../config/conexion.php";

class Usuario {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
    }

    public function getUsuarioLogin(string $email, string $clave): ?array {
        try {
            $sql = "SELECT * 
                    FROM usuario WHERE correo = :email AND clave = :clave";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':clave', $clave);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) {
            error_log("Error getUsuarioLogin: " . $e->getMessage());
            return null;
        }
    }

    public function insertarUsuario(string $correo, string $clave, string $rol = 'ADMINISTRADOR'): bool
    {
        try {
            $sql = 'INSERT INTO usuario (correo, clave, rol) VALUES (:correo, :clave, :rol)';
            $stmt = $this->conexion->prepare($sql);

            return (bool) $stmt->execute([
                ':correo' => $correo,
                ':clave'  => $clave,
                ':rol'    => $rol,
            ]);
        } catch (Throwable $e) {
            error_log('Error insertarUsuario: ' . $e->getMessage());
            return false;
        }
    }
}
