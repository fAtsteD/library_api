<?php

namespace App\Models;

use App\DB\Connection;
use RuntimeException;

/**
 * Class for books
 */
class User extends ModelDB
{
    /**
     * Name of table for model
     *
     * @var string
     */
    public static $tablename = 'user';

    /**
     * Username. Unique
     *
     * @var string
     */
    protected $username = '';

    /**
     * Password
     *
     * @var string
     */

    protected $password = '';
    /**
     * Token
     *
     * @var string
     */
    protected $token = '';

    /**
     * Length of token
     */
    protected const TOKEN_LENGTH = 32;

    /**
     * Return username
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Return token
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Set username
     *
     * @param sting $username
     * @return void
     */
    public function setUsername(string $username)
    {
        return $this->username = $username;
    }

    /**
     * Set password
     *
     * @param sting $password
     * @return void
     */
    public function setPassword(string $password)
    {
        return $this->password = md5($password);
    }

    /**
     * Check password
     *
     * @param sting $password
     * @return void
     */
    public function isEqualPassword(string $password)
    {
        return $this->password === md5($password);
    }

    /**
     * @inheritDoc
     **/
    public function save()
    {
        $conn = Connection::getConnection()->getPDO();

        if ($this->password === "") {
            throw new RuntimeException("Password does not set", 404);
        }

        $this->token = md5($this->username . $this->password);

        // Insert/update books
        if ($this->id == 0) {
            $query = "INSERT INTO " . self::$tablename . "(username,password,token) VALUES (:username,:password,:token);";
            if (!$conn->prepare($query)->execute([':username' => $this->username, ':password' => $this->password, ':token' => $this->token])) {
                throw new RuntimeException("Cannot insert data", 404);
            }

            $this->id = (int) $conn->lastInsertId();
        } else {
            $query = "INSERT INTO " . self::$tablename . "(id,username,password,token) VALUES (:id,:username,:password,:token) ON DUPLICATE KEY UPDATE username = :username, password=:password, token=:token;";
            if (!$conn->prepare($query)->execute([':id' => $this->id, ':username' => $this->username, ':password' => $this->password, ':token' => $this->token])) {
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
    }

    /**
     * @inheritDoc
     */
    public static function findAll()
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

        $users = [];
        for ($i = 0; $i < count($result); $i++) {
            $users[$i] = new User();
            $users[$i]->id = $result[$i]['id'];
            $users[$i]->username = $result[$i]['username'];
            $users[$i]->password = $result[$i]['password'];
            $users[$i]->token = $result[$i]['token'];
        }

        return $users;
    }

    /**
     * @inheritDoc
     * @return User|null
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

        $user = new User();
        $user->id = $result[0]['id'];
        $user->username = $result[0]['username'];
        $user->password = $result[0]['password'];
        $user->token = $result[0]['token'];

        return $user;
    }

    /**
     * Find in db user by username
     *
     * @param string $username
     * @return User|null
     */
    static public function findByUsername($username)
    {
        $query = "SELECT * FROM " . self::$tablename . " WHERE username=:username;";
        $conn = Connection::getConnection()->getPDO();
        $conn = $conn->prepare($query);
        if (!$conn->execute([':username' => $username])) {
            throw new RuntimeException("Cannot select data", 404);
        }

        $result = $conn->fetchAll();

        if (empty($result)) {
            return null;
        }

        $user = new User();
        $user->id = $result[0]['id'];
        $user->username = $result[0]['username'];
        $user->password = $result[0]['password'];
        $user->token = $result[0]['token'];

        return $user;
    }

    /**
     * Find in db user by token
     *
     * @param string $name
     * @return User|null
     */
    static public function findByToken($token)
    {
        $query = "SELECT * FROM " . self::$tablename . " WHERE token=:token;";
        $conn = Connection::getConnection()->getPDO();
        $conn = $conn->prepare($query);
        if (!$conn->execute([':token' => $token])) {
            throw new RuntimeException("Cannot select data", 404);
        }

        $result = $conn->fetchAll();

        if (empty($result)) {
            return null;
        }

        $user = new User();
        $user->id = $result[0]['id'];
        $user->username = $result[0]['username'];
        $user->password = $result[0]['password'];
        $user->token = $result[0]['token'];

        return $user;
    }
}
