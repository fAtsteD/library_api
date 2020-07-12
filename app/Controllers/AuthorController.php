<?php

namespace App\Controllers;

use App\App;
use App\Models\Author;
use RuntimeException;

/**
 * API controller for author
 * 
 * URL: /api/author
 */
class AuthorController extends ApiController
{
    /**
     * @inheritdoc
     * @throws RuntimeException
     */
    public function indexAction(): string
    {
        $response = [];
        $authors = Author::findAll();

        if (empty($authors)) {
            throw new RuntimeException("Authors does not exist", 404);
        }

        foreach ($authors as $author) {
            $response[] = [
                'id' => $author->getId(),
                'name' => $author->getName(),
            ];
        }

        return $this->response([
            'status' => 'success',
            'authors' => $response
        ], 200);
    }

    /**
     * @inheritdoc
     */
    public function viewAction(): string
    {
        $author = $this->isExistAuthor();

        return $this->response([
            'status' => 'success',
            'id' => $author->getId(),
            'name' => $author->getName(),
        ], 200);
    }

    /**
     * @inheritdoc
     * @throws RuntimeException
     */
    public function createAction(): string
    {
        if (!isset(App::$requestParams['name'])) {
            throw new RuntimeException("Wrong params", 404);
        }

        if (!is_null(Author::findByName(App::$requestParams['name']))) {
            throw new RuntimeException("Author exists", 404);
        }

        $author = new Author();
        $author->setName(App::$requestParams['name']);

        $author->save();

        return $this->response(['status' => 'success'], 200);
    }

    /**
     * @inheritdoc
     */
    public function updateAction(): string
    {
        $author = $this->isExistAuthor();

        if (!isset(App::$requestParams['name'])) {
            throw new RuntimeException("Wrong params", 404);
        }

        $author->setName(App::$requestParams['name']);

        $author->save();

        return $this->response(['status' => 'success'], 200);
    }

    /**
     * @inheritdoc
     */
    public function deleteAction(): string
    {
        $author = $this->isExistAuthor();

        $author->delete();

        return $this->response(['status' => 'success'], 200);
    }

    /**
     * Check if request params is right and book exists
     *
     * @return Author
     * @throws RuntimeException
     */
    private function isExistAuthor()
    {
        $id = parse_url(App::$requestUri[0])['path'];
        if (is_numeric($id)) {
            $author = Author::findById(intval($id));
        } else {
            throw new RuntimeException("Wrong params", 404);
        }

        if (is_null($author)) {
            throw new RuntimeException("Wrong params", 404);
        }

        return $author;
    }
}
