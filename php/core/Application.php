<?php
/**
 * Created by PhpStorm.
 * User: nnicu
 * Date: 13.05.2019
 * Time: 19:38
 */

class Application
{
    private $controller = 'homeController';
    private $action = 'index';
    private $params = [];

    public function __construct()
    {
        $this->prepareURL();
        if (file_exists(CONTROLLER . $this->controller . '.php')) {
            $this->controller = new $this->controller;
            if (method_exists($this->controller, $this->action)) {
                Database::setConnection();
                call_user_func_array([$this->controller, $this->action], $this->params);
            }
        } else {
            http_response_code(404);
            include( VIEW . '404.phtml');
        }
    }

    protected function prepareURL()
    {
        $request = trim($_SERVER['REQUEST_URI'], '/');
        if (!empty($request)) {
            $url = explode('/', $request);
            $this->controller = isset($url[0]) ? $url[0] . 'Controller' : 'homeController';
            $this->action = isset($url[1]) ? $url[1] : 'index';
            unset($url[0], $url[1]);
            $this->params = !empty($url) ? array_values($url) : [];
        }
        else self::logger("Request uri is empty.",__FILE__, "ERROR");
    }

    public static function redirectTo($path = "")
    {
        if (empty($path)) {
            header("Location: /home");
        } else header("Location: $path");
        exit();
    }

    public static function logger($message = "Error message", $file = "", $level = "WARNING")
    {
        $date = date("Y-m-d h:m:s");
        $message = "[$date][$file][$level] " . $message . PHP_EOL;
        error_log($message);
    }
}