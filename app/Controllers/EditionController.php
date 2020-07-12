<?php

namespace App\Controllers;

use App\App;
use App\Models\Edition;
use RuntimeException;

/**
 * API controller for edition
 * 
 * URL: /api/edition
 */
class EditionController extends ApiController
{
    /**
     * @inheritdoc
     * @throws RuntimeException
     */
    public function indexAction(): string
    {
        $response = [];
        $editions = Edition::findAll();

        if (empty($editions)) {
            throw new RuntimeException("Editions does not exist", 404);
        }

        foreach ($editions as $edition) {
            $response[] = [
                'id' => $edition->getId(),
                'name' => $edition->getName(),
            ];
        }

        return $this->response([
            'status' => 'success',
            'editions' => $response
        ], 200);
    }

    /**
     * @inheritdoc
     */
    public function viewAction(): string
    {
        $edition = $this->isExistEdition();

        return $this->response([
            'status' => 'success',
            'id' => $edition->getId(),
            'name' => $edition->getName(),
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

        if (!is_null(Edition::findByName(App::$requestParams['name']))) {
            throw new RuntimeException("Edition exists", 404);
        }

        $edition = new Edition();
        $edition->setName(App::$requestParams['name']);

        $edition->save();

        return $this->response(['status' => 'success'], 200);
    }

    /**
     * @inheritdoc
     */
    public function updateAction(): string
    {
        $edition = $this->isExistEdition();

        if (!isset(App::$requestParams['name'])) {
            throw new RuntimeException("Wrong params", 404);
        }

        $edition->setName(App::$requestParams['name']);

        $edition->save();

        return $this->response(['status' => 'success'], 200);
    }

    /**
     * @inheritdoc
     */
    public function deleteAction(): string
    {
        $edition = $this->isExistEdition();

        $edition->delete();

        return $this->response(['status' => 'success'], 200);
    }

    /**
     * Check if request params is right and book exists
     *
     * @return Edition
     * @throws RuntimeException
     */
    private function isExistEdition()
    {
        $id = parse_url(App::$requestUri[0])['path'];
        if (is_numeric($id)) {
            $edition = Edition::findById(intval($id));
        } else {
            throw new RuntimeException("Wrong params", 404);
        }

        if (is_null($edition)) {
            throw new RuntimeException("Wrong params", 404);
        }

        return $edition;
    }
}
