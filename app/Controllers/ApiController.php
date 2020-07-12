<?php

namespace App\Controllers;

/**
 * Abstract class for API
 */
abstract class ApiController
{
    /**
     * Response for request
     *
     * @param array $data
     * @param int $statusCode
     * @return string
     */
    protected function response(array $data, int $statusCode)
    {
        header('HTTP/1.1 ' . $statusCode . " " . $this->getStatus($statusCode));
        return json_encode($data);
    }

    /**
     * Return message for status by code
     *
     * @param int $code
     * @return string
     */
    private function getStatus($statusCode)
    {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return $status[$statusCode];
    }

    /**
     * API action view all items
     *
     * @return string
     */
    abstract public function indexAction(): string;

    /**
     * API action view
     *
     * @return string
     */
    abstract public function viewAction(): string;

    /**
     * API action create
     *
     * @return string
     */
    abstract public function createAction(): string;

    /**
     * API action update
     *
     * @return string
     */
    abstract public function updateAction(): string;

    /**
     * API action delete
     *
     * @return string
     */
    abstract public function deleteAction(): string;
}
