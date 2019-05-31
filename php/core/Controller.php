<?php

class Controller
{
    protected $view;
    protected $model;

    public function view($viewName, $data = [])
    {
        $this->view = new View($viewName, $data);
        return $this->view;
    }

    public function model($modelName)
    {
        if (file_exists(MODEL . $modelName . '.php')) {
            require MODEL . $modelName . '.php';
            $this->model = new $modelName;
        } else {
            Application::logger("Model $modelName is not exist in model(modelName)", __CLASS__, "ERROR");
            Application::redirectTo("/home/internal_error");
        }
    }

    protected function bad_request()
    {
        http_response_code(400);
        $params = array();
        $params['title'] = "Error 400";
        $params['content'] = "Bad request";
        $this->view('home' . DIRECTORY_SEPARATOR . 'infoPage', $params);
        $this->view->render();
        exit();
    }
}
