<?php

namespace App\Models;

use App\DB\Connection;
use RuntimeException;

/**
 * Class for authors
 */
class Author extends ModelDB
{
    /**
     * Name. Unique
     *
     * @var string
     */
    protected $name;

    public function __construct(string $name)
    {
        self::$tablename = 'author';
        $this->name = $name;
    }

    /**
     * Return id of author
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Return name of author
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set name of author
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
     * Find in db author by id
     *
     * @param int $id
     * @return Author
     */
    static public function findById($id): Author
    {
        // TODO: find by id
    }

    /**
     * Find in db author by name
     *
     * @param string $name
     * @return Author
     */
    static public function findByName($name): Author
    {
        TODO: find author by name
    }
}
