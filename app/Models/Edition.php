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
    protected $name = '';

    public function __construct()
    {
        self::$tablename = 'edition';
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
            throw new RuntimeException("Cannot insert/update data", 404);
        }
    }

    /**
     * Find in db all editions
     *
     * @return array
     */
    static public function findAll(): array
    {
        $query = "SELECT * FROM " . self::$tablename . ";";
        $conn = Connection::getConnection()->getPDO();
        $conn = $conn->prepare($query);
        if (!$conn->execute()) {
            throw new RuntimeException("Cannot select data", 404);
        }

        $result = $conn->fetchAll();

        if (empty($result)) {
            return [];
        }

        $editions = [];
        for ($i = 0; $i < count($result); $i++) {
            $editions[$i] = new Edition();
            $editions[$i]->id = $result[$i]['id'];
            $editions[$i]->name = $result[$i]['name'];
        }

        return $editions;
    }

    /**
     * Find in db edition by id
     *
     * @param int $id
     * @return Edition|null
     */
    static public function findById($id): Edition
    {
        $query = "SELECT * FROM " . self::$tablename . " WHERE id=:id;";
        $conn = Connection::getConnection()->getPDO();
        $conn = $conn->prepare($query);
        if (!$conn->execute([':id' => $id])) {
            throw new RuntimeException("Cannot select data", 404);
        }

        $result = $conn->fetchAll();

        if (empty($result)) {
            return null;
        }

        $edition = new Edition();
        $edition->id = $result[0]['id'];
        $edition->name = $result[0]['name'];

        return $edition;
    }

    /**
     * Find in db edition by name
     *
     * @param string $name
     * @return Edition|null
     */
    static public function findByName($name): Edition
    {
        $query = "SELECT * FROM " . self::$tablename . " WHERE name=:name;";
        $conn = Connection::getConnection()->getPDO();
        $conn = $conn->prepare($query);
        if (!$conn->execute([':name' => $name])) {
            throw new RuntimeException("Cannot select data", 404);
        }

        $result = $conn->fetchAll();

        if (empty($result)) {
            return null;
        }

        $edition = new Edition();
        $edition->id = $result[0]['id'];
        $edition->name = $result[0]['name'];

        return $edition;
    }
}
