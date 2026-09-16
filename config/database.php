<?php

class Database {
    private static $instance = null;
    private $connection;

    // Configuración de la base de datos
    private $host = 'localhost';
    private $dbname = 'cashly_db';
    private $username = 'root';
    private $password = ''; // Coloca aquí tu clave de MySQL si tienes una

    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    /**
     * Obtiene la instancia única de la conexión (Patrón Singleton).
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Retorna la conexión PDO activa.
     */
    public static function getConnection() {
        return self::getInstance()->connection;
    }

    // Prevenir la clonación del objeto
    private function __clone() {}
}