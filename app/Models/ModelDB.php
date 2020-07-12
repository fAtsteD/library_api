<?php

namespace App\Models;

use App\DB\Connection;
use RuntimeException;

/**
 * Abstract class for model
 */
abstract class ModelDB
{
    /**
     * Name of table for model
     *
     * @var string
     */
    public static $tablename = '';

    /**
     * Id of object in db
     *
     * @var int
     */
    protected $id = 0;

    /**
     * Delete data from db
     * 
     * @return void
     **/
    public function delete()
    {
        $query = "DELETE FROM " . self::$tablename . " WHERE id = ?";
        $conn = Connection::getConnection()->getPDO();
        if (!$conn->prepare($query)->execute($this->id)) {
            throw new RuntimeException("Cannot delete data");
        }
    }
}
