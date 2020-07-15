<?php

namespace App\Auth;

use App\App;
use App\Models\User;

/**
 * Authenticate user
 */
class Authentication
{
    /**
     * Object of user that is authenticated
     *
     * @var User|null
     */
    private $user = null;

    public function __construct()
    {
        if (isset(App::$requestParams['user-token'])) {
            $this->user = User::findByToken(App::$requestParams['user-token']);
        }
    }

    /**
     * Return object of user that is authenticated
     *
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Is user authenticated
     *
     * @return bool
     */
    public function isGuest()
    {
        return is_null($this->user);
    }
}
