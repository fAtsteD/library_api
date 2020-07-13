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
        $query = "DELETE FROM " . get_called_class()::$tablename . " WHERE id = :id;";
        $conn = Connection::getConnection()->getPDO();
        if (!$conn->prepare($query)->execute([':id' => $this->id])) {
            throw new RuntimeException("Cannot delete data", 404);
        }
    }

    /**
     * Save data to db
     *
     * @return void
     **/
    public abstract function save();

    /**
     * Find in db all entities
     *
     * @return array
     */
    public abstract static function findAll();

    /**
     * Find in db entity by id
     *
     * @param int $id
     * @return ModelDB|null
     */
    public abstract static function findById(int $id);
}
