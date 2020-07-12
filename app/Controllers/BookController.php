<?php

namespace App\Controllers;

use App\App;
use App\Models\Author;
use App\Models\Book;
use App\Models\Edition;
use RuntimeException;

class BookController extends ApiController
{
    /**
     * @inheritdoc
     * @throws RuntimeException
     */
    public function indexAction(): string
    {
        $response = [];
        $books = Book::findAll();

        if (empty($books)) {
            throw new RuntimeException("Books does not exist", 404);
        }

        foreach ($books as $book) {
            $authors = [];
            foreach ($book->getAuthors() as $author) {
                $authors[] = $author->getName();
            }

            $response[] = [
                'id' => $book->getId(),
                'name' => $book->getName(),
                'edition' => $book->getEdition()->getName(),
                'authors' => $authors,
            ];
        }

        return $this->response($response, 200);
    }

    /**
     * @inheritdoc
     */
    public function viewAction(): string
    {
        $book = $this->isExistBook();

        $authors = [];
        foreach ($book->getAuthors() as $author) {
            $authors[] = $author->getName();
        }

        return $this->response([
            'status' => 'success',
            'id' => $book->getId(),
            'name' => $book->getName(),
            'edition' => $book->getEdition()->getName(),
            'authors' => $authors,
        ], 200);
    }

    /**
     * @inheritdoc
     * @throws RuntimeException
     */
    public function createAction(): string
    {
        if (!(isset(App::$requestParams['name']) && isset(App::$requestParams['edition']) && isset(App::$requestParams['authors']))) {
            throw new RuntimeException("Wrong params", 404);
        }

        $book = new Book();
        $book->setName(App::$requestParams['name']);

        // Set edition, create its if it does not exist
        $editionName = App::$requestParams['edition'];
        $edition = Edition::findByName($editionName);

        if (is_null($edition)) {
            $edition = new Edition();
            $edition->setName($editionName);
            $edition->save();
        }

        $book->setEdition($edition);

        // Set authors, create them if they do not exist
        $authors = [];
        foreach (App::$requestParams['authors'] as $authorName) {
            $author = Author::findByName($authorName);

            if (is_null($author)) {
                $author = new Author();
                $author->setName($authorName);
                $author->save();
            }

            $authors[] = $author;
        }

        $book->setAuthors($authors);

        return $this->response(['status' => 'success'], 200);
    }

    /**
     * @inheritdoc
     */
    public function updateAction(): string
    {
        $book = $this->isExistBook();

        if (!(isset(App::$requestParams['name']) || isset(App::$requestParams['edition']) || isset(App::$requestParams['authors']))) {
            throw new RuntimeException("Wrong params", 404);
        }

        // Change name
        if (isset(App::$requestParams['name'])) {
            $book->setName(App::$requestParams['name']);
        }

        // Change edition
        if (isset(App::$requestParams['edition'])) {
            // Set edition, create its if it does not exist
            $editionName = App::$requestParams['edition'];
            $edition = Edition::findByName($editionName);

            if (is_null($edition)) {
                $edition = new Edition();
                $edition->setName($editionName);
                $edition->save();
            }

            $book->setEdition($edition);
        }

        // Change authors
        if (isset(App::$requestParams['authors'])) {
            // Set authors, create them if they do not exist
            $authors = [];
            foreach (App::$requestParams['authors'] as $authorName) {
                $author = Author::findByName($authorName);

                if (is_null($author)) {
                    $author = new Author();
                    $author->setName($authorName);
                    $author->save();
                }

                $authors[] = $author;
            }

            $book->setAuthors($authors);
        }

        return $this->response(['status' => 'success'], 200);
    }

    /**
     * @inheritdoc
     */
    public function deleteAction(): string
    {
        $book = $this->isExistBook();

        $book->delete();

        return $this->response(['status' => 'success'], 200);
    }

    /**
     * Check if request params is right and book exists
     *
     * @return Book
     * @throws RuntimeException
     */
    private function isExistBook()
    {
        if (isset(App::$requestParams['id'])) {
            $book = Book::findById(App::$requestParams['id']);
        } elseif (isset(App::$requestParams['name'])) {
            $book = Book::findById(App::$requestParams['name']);
        }
        else {
            throw new RuntimeException("Wrong params", 404);
        }

        if (is_null($book)) {
            throw new RuntimeException("Wrong params", 404);
        }

        return $book;
    }
}
