<?php

namespace App\Models;

use App\DB\Connection;
use RuntimeException;

/**
 * Class for editions
 */
class Edition extends ModelDB
{
    /**
     * Name of edition. Unique
     *
     * @var string
     */
    protected $name;

    public function __construct(string $name)
    {
        self::$tablename = 'edition';
        $this->name = $name;
    }

    /**
     * Return id of edition
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Return name of edition
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set name of edition
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name)
    {
        return $this->name = $name;
    }

    /**
     * Save data to db
     *
     * @return void
     **/
    public function save()
    {
        $query = "INSERT INTO " . self::$tablename . "(id,name) VALUES (:id,:name) ON DUPLICATE KEY UPDATE name = :name;";
        $conn = Connection::getConnection()->getPDO();
        if (!$conn->prepare($query)->execute([':id' => $this->id, ':name' => $this->name])) {
            throw new RuntimeException("Cannot insert/update data");
        }
    }

    /**
     * Find in db edition by id
     *
     * @param int $id
     * @return Edition
     */
    static public function findById($id): Edition
    {
        // TODO: find by id
    }

    /**
     * Find in db edition by name
     *
     * @param string $name
     * @return Edition
     */
    static public function findByName($name): Edition
    {
        $query = "SELECT * FROM" . self::TABLENAME . "WHERE name=" . $name . ";";
        $conn = Connection::getConnection()->getPDO();
        if (!$conn->prepare($query)->execute([':id' => $this->id, ':name' => $this->name])) {
            throw new RuntimeException("Cannot delete data");
        }
    }
}
