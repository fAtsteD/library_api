<?php

namespace App;

use App\Auth\Authentication;
use Exception;
use RuntimeException;

/**
 * Main class for app
 */
class App
{
    /**
     * Uri
     *
     * @var array
     */
    public static $requestUri = [];

    /**
     * Params for action
     *
     * @var array
     */
    public static $requestParams = [];

    /**
     * Object of auth
     *
     * @var Authentication
     */
    public static $auth = null;

    /**
     * Action that has to be used
     *
     * @var string
     */
    private $action = '';

    /**
     * Method of request
     *
     * @var string
     */
    private $method = '';

    /**
     * Set headers, parse uri, define method of request
     * 
     * @throws Exception
     */
    public function __construct()
    {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

        // Parse Uri
        self::$requestUri = $this->getRequestUriPath();
        self::$requestParams = $_REQUEST;

        // Define method of request
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header", 404);
            }
        }

        // Authenticate user
        self::$auth = new Authentication();
    }

    /**
     * Start app
     *
     * @return string
     * @throws RuntimeException
     */
    public function run()
    {
        if (array_shift(self::$requestUri) !== 'api') {
            throw new RuntimeException('API Not Found', 404);
        }

        $controller = $this->getController(array_shift(self::$requestUri));

        if ($controller === null) {
            throw new RuntimeException('Invalid controller', 404);
        }

        // Define action
        $this->action = $this->getAction();

        // Check if method exists
        if (method_exists($controller, $this->action)) {
            return $controller->{$this->action}();
        } else {
            throw new RuntimeException('Invalid Method', 405);
        }
    }

    /**
     * Return name of action
     *
     * @return string|null
     */
    private function getAction()
    {
        switch ($this->method) {
            case 'GET':
                if (self::$requestUri) {
                    return 'viewAction';
                } else {
                    return 'indexAction';
                }
                break;
            case 'POST':
                return 'createAction';
                break;
            case 'PUT':
                return 'updateAction';
                break;
            case 'DELETE':
                return 'deleteAction';
                break;
            default:
                return null;
        }
    }

    /**
     * Return controller from part of uri
     *
     * @param string $path
     * @return mixed one of the controllers
     */
    private function getController(string $path)
    {
        $controllerName = "App\\Controllers\\" . ucfirst($path) . "Controller";
        if (class_exists($controllerName)) {
            return new $controllerName();
        }

        return null;
    }

    /**
     * Parse url and return array of path
     *
     * @return array
     */
    private function getRequestUriPath()
    {
        $uri = parse_url($_SERVER['REQUEST_URI']);
        return explode('/', trim($uri['path'], '/'));
    }
}
