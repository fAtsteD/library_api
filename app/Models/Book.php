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
     * Name of edition. Unique
     *
     * @var string
     */
    protected $name;

    /**
     * Id of edition
     *
     * @var int
     */
    protected $editionId;

    /**
     * Id of autors
     *
     * @var array
     */
    protected $authorIds;

    /**
     * Tablename for many to many with authors
     *
     * @var string
     */
    protected static $tablenameBookAuthor = 'book_author';

    /**
     * Initialize data
     *
     * @param string $name
     * @param Edition $edition
     * @param array $authors
     */
    public function __construct(string $name, Edition $edition, array $authors)
    {
        self::$tablename = 'book';
        $this->name = $name;
        $this->editionId = $edition->getId();
        foreach ($authors as $author) {
            $this->authorIds[] = $author->getId();
        }
    }

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
     * @return Edition
     */
    public function getEdition(): Edition
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
        $this->editionId = $edition->getId();
    }

    /**
     * Set authors of book
     *
     * @param array $authors
     * @return void
     */
    public function setAuthors(array $authors)
    {
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
        if ($this->id === 0) {
            $query = "INSERT INTO " . self::$tablename . "(name,edition_id) VALUES (:name,:edition_id);";
            if (!$conn->prepare($query)->execute([':name' => $this->name, ':edition_id' => $this->editionId])) {
                throw new RuntimeException("Cannot insert data");
            }

            $this->id = (int) $conn->lastInsertId();
        } else {
            $query = "INSERT INTO " . self::$tablename . "(id,name,edition_id) VALUES (:id,:name,:edition_id) ON DUPLICATE KEY UPDATE name = :name, edition_id=:edition_id;";
            if (!$conn->prepare($query)->execute([':id' => $this->id, ':name' => $this->name, ':edition_id' => $this->editionId])) {
                throw new RuntimeException("Cannot insert/update data");
            }
        }

        // Insert/update authors
        foreach ($this->authorIds as $authorId) {
            $query = "INSERT INTO " . self::$tablenameBookAuthor . "(book_id,author_id) VALUES (:book_id,:author_id) ON DUPLICATE KEY UPDATE book_id = :book_id, author_id=:author_id;";
            if (!$conn->prepare($query)->execute([':book_id' => $this->id, ':author_id' => $authorId])) {
                throw new RuntimeException("Cannot insert/update data");
            }
        }
    }

    /**
     * Find in db book by id
     *
     * @param int $id
     * @return Book
     */
    static public function findById($id): Book
    {
        // TODO: find by id
    }

    /**
     * Find in db book by name
     *
     * @param string $name
     * @return Book
     */
    static public function findByName($name): Book
    {
        // TODO: find by name
    }
}
