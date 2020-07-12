<?php

namespace App\Models;

use App\DB\Connection;
use RuntimeException;

/**
 * Class for books
 */
class Book extends ModelDB
{
    /**
     * Name of table for model
     *
     * @var string
     */
    public static $tablename = 'book';

    /**
     * Name of book. Unique
     *
     * @var string
     */
    protected $name = '';

    /**
     * Id of edition
     *
     * @var int
     */
    protected $editionId = 0;

    /**
     * Id of autors
     *
     * @var array
     */
    protected $authorIds = [];

    /**
     * Tablename for many to many with authors
     *
     * @var string
     */
    public static $tablenameBookAuthor = 'book_author';

    /**
     * Return id of book
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Return name of book
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return edition of book
     *
     * @return Edition|null
     */
    public function getEdition()
    {
        return Edition::findById($this->editionId);
    }

    /**
     * Return authors of book
     *
     * @return array
     */
    public function getAuthors(): array
    {
        $authors = [];
        foreach ($this->authorIds as $authorId) {
            $authors[] = Author::findById($authorId);
        }
        return $authors;
    }

    /**
     * Set name of edition
     *
     * @param sting $book
     * @return void
     */
    public function setName(string $book)
    {
        return $this->name = $book;
    }

    /**
     * Set edition of book
     *
     * @param Edition $edition
     * @return void
     */
    public function setEdition(Edition $edition)
    {
        $this->editionId = is_null($edition) ? 0 : $edition->getId();
    }

    /**
     * Set authors of book
     *
     * @param array $authors
     * @return void
     */
    public function setAuthors(array $authors)
    {
        if (empty($authors)) {
            $this->authorIds[] = [];
            return;
        }

        // Delete old authors
        if (!empty($this->authorIds)) {
            $this->deleteAuthors();
        }

        foreach ($authors as $author) {
            $this->authorIds[] = $author->getId();
        }
    }

    /**
     * Save data to db
     *
     * @return void
     **/
    public function save()
    {
        $conn = Connection::getConnection()->getPDO();

        // Insert/update books
        if ($this->id == 0) {
            $query = "INSERT INTO " . self::$tablename . "(name,edition_id) VALUES (:name,:edition_id);";
            if (!$conn->prepare($query)->execute([':name' => $this->name, ':edition_id' => $this->editionId])) {
                throw new RuntimeException("Cannot insert data", 404);
            }

            $this->id = (int) $conn->lastInsertId();
        } else {
            $query = "INSERT INTO " . self::$tablename . "(id,name,edition_id) VALUES (:id,:name,:edition_id) ON DUPLICATE KEY UPDATE name = :name, edition_id=:edition_id;";
            if (!$conn->prepare($query)->execute([':id' => $this->id, ':name' => $this->name, ':edition_id' => $this->editionId])) {
                throw new RuntimeException("Cannot insert/update data", 404);
            }
        }

        // Insert/update authors
        foreach ($this->authorIds as $authorId) {
            $query = "INSERT INTO " . self::$tablenameBookAuthor . "(book_id,author_id) VALUES (:book_id,:author_id) ON DUPLICATE KEY UPDATE book_id = :book_id, author_id=:author_id;";
            if (!$conn->prepare($query)->execute([':book_id' => $this->id, ':author_id' => $authorId])) {
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

        $this->deleteAuthors();
    }

    /**
     * Find in db all books
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

        $books = [];
        for ($i = 0; $i < count($result); $i++) {
            $books[$i] = new Book();
            $books[$i]->id = $result[$i]['id'];
            $books[$i]->name = $result[$i]['name'];
            $books[$i]->editionId = $result[$i]['edition_id'];

            $query = "SELECT * FROM " . self::$tablenameBookAuthor . " WHERE book_id=:book_id;";
            $conn = Connection::getConnection()->getPDO();
            $conn = $conn->prepare($query);
            if (!$conn->execute([':book_id' => $books[$i]->id])) {
                throw new RuntimeException("Cannot select data", 404);
            }

            $resultBookAuthors = $conn->fetchAll();

            if (!empty($resultBookAuthors)) {
                foreach ($resultBookAuthors as $authorId) {
                    $books[$i]->authorIds[] = $authorId['author_id'];
                }
            }
        }

        return $books;
    }

    /**
     * Find in db book by id
     *
     * @param int $id
     * @return Book|null
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

        $book = new Book();
        $book->id = $result[0]['id'];
        $book->name = $result[0]['name'];
        $book->editionId = $result[0]['edition_id'];

        $query = "SELECT * FROM " . self::$tablenameBookAuthor . " WHERE book_id=:book_id;";
        $conn = Connection::getConnection()->getPDO();
        $conn = $conn->prepare($query);
        if (!$conn->execute([':book_id' => $book->id])) {
            throw new RuntimeException("Cannot select data", 404);
        }

        $resultBookAuthors = $conn->fetchAll();

        if (!empty($resultBookAuthors)) {
            foreach ($resultBookAuthors as $authorId) {
                $book->authorIds[] = $authorId['author_id'];
            }
        }

        return $book;
    }

    /**
     * Find in db book by name
     *
     * @param string $name
     * @return Book|null
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

        $book = new Book();
        $book->id = $result[0]['id'];
        $book->name = $result[0]['name'];
        $book->editionId = $result[0]['edition_id'];

        $query = "SELECT * FROM " . self::$tablenameBookAuthor . " WHERE book_id=:book_id;";
        $conn = Connection::getConnection()->getPDO();
        $conn = $conn->prepare($query);
        if (!$conn->execute([':book_id' => $book->id])) {
            throw new RuntimeException("Cannot select data", 404);
        }

        $resultBookAuthors = $conn->fetchAll();

        if (!empty($resultBookAuthors)) {
            foreach ($resultBookAuthors as $authorId) {
                $book->authorIds[] = $authorId['author_id'];
            }
        }

        return $book;
    }

    /**
     * Delete authors for the book
     *
     * @return void
     */
    private function deleteAuthors()
    {
        $query = "DELETE FROM " . self::$tablenameBookAuthor . " WHERE book_id = :book_id;";
        $conn = Connection::getConnection()->getPDO();
        if (!$conn->prepare($query)->execute([':book_id' => $this->id])) {
            throw new RuntimeException("Cannot delete data", 404);
        }
    }
}
