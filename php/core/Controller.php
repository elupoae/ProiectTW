<?php
/**
 * Created by PhpStorm.
 * User: nnicu
 * Date: 13.05.2019
 * Time: 20:35
 */

class Controller
{
    protected $view;
    protected $model;

    public function view($viewName, $data = [])
    {
        $this->view = new View($viewName, $data);
        return $this->view;
    }

    public function model($modelName, $data = [])
    {
        if (file_exists(MODEL . $modelName . '.php')) {
            require MODEL . $modelName . '.php';
            $this->model = new $modelName;
        }
    }

    protected function bad_request()
    {
        $params = array();
        $params['title'] = "Error 400";
        $params['content'] = "Bad request";
        $this->view('home' . DIRECTORY_SEPARATOR . 'infoPage', $params);
        $this->view->render();
        exit();
    }
}