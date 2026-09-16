<?php
require_once __DIR__ . '/../../config/database.php';

class Usuario {
    private $pdo;

    public function __construct() {
        // Al ser Singleton con constructor privado, llamamos a su método estático
        if (method_exists('Database', 'getInstance')) {
            $this->pdo = Database::getInstance()->getConnection();
        } elseif (method_exists('Database', 'getConexion')) {
            $this->pdo = Database::getConexion();
        } elseif (method_exists('Database', 'conectar')) {
            $this->pdo = Database::conectar();
        } else {
            // Si el método estático de tu Database se llama distinto, ajústalo aquí
            $this->pdo = Database::obtenerConexion(); 
        }
    }

    public function obtenerPorEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($nombre, $email, $password) {
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)");
        return $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':password' => $password
        ]);
    }
}