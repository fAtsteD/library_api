<?php

namespace App\DB;

use PDO;

/**
 * Create connection to db
 */
class Connection
{
    /**
     * Instance of connection
     *
     * @var Connection
     */
    private static $instance = null;

    /**
     * Instance of object pdo
     *
     * @var PDO
     */
    private $connectionObject;

    private function __construct()
    {
        $config = $this->getConfig();
        $this->connectionObject = new PDO("mysql:host=" . $config['host'] . ";dbname=" . $config['dbname'], $config['username'], $config['password']);
    }

    /**
     * Create only one connection to db
     *
     * @return Connection
     */
    public static function getConnection(): Connection
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Return PDO object
     *
     * @return PDO
     */
    public function getPDO()
    {
        return $this->connectionObject;
    }

    /**
     * Return config for db
     *
     * @return array
     */
    private function getConfig(): array
    {
        return require __DIR__ . '/config.php';
    }

    private function __clone()
    {
    }
}
