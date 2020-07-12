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
     * Name of table for model
     *
     * @var string
     */
    public static $tablename = 'author';

    /**
     * Name. Unique
     *
     * @var string
     */
    protected $name = '';

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
        if ($this->id == 0) {
            $query = "INSERT INTO " . self::$tablename . "(name) VALUES (:name);";
            $conn = Connection::getConnection()->getPDO();
            if (!$conn->prepare($query)->execute([':name' => $this->name])) {
                throw new RuntimeException("Cannot insert data", 404);
            }

            $this->id = $conn->lastInsertId();
        } else {
            $query = "INSERT INTO " . self::$tablename . "(id,name) VALUES (:id,:name) ON DUPLICATE KEY UPDATE name = :name;";
            $conn = Connection::getConnection()->getPDO();
            if (!$conn->prepare($query)->execute([':id' => $this->id, ':name' => $this->name])) {
                throw new RuntimeException("Cannot insert/update data", 404);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function delete()
    {
        parent::delete();

        $query = "DELETE FROM " . Book::$tablenameBookAuthor . " WHERE author_id = :author_id;";
        $conn = Connection::getConnection()->getPDO();
        if (!$conn->prepare($query)->execute(['author_id' => $this->id])) {
            throw new RuntimeException("Cannot delete data", 404);
        }
    }

    /**
     * Find in db all authors
     *
     * @return array
     */
    static public function findAll()
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

        $authors = [];
        for ($i = 0; $i < count($result); $i++) {
            $authors[$i] = new Author();
            $authors[$i]->id = $result[$i]['id'];
            $authors[$i]->name = $result[$i]['name'];
        }

        return $authors;
    }

    /**
     * Find in db author by id
     *
     * @param int $id
     * @return Author|null
     */
    static public function findById($id)
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

        $author = new Author();
        $author->id = $result[0]['id'];
        $author->name = $result[0]['name'];

        return $author;
    }

    /**
     * Find in db author by name
     *
     * @param string $name
     * @return Author|null
     */
    static public function findByName($name)
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

        $author = new Author($result[0]['name']);
        $author->id = $result[0]['id'];
        $author->name = $result[0]['name'];

        return $author;
    }
}
