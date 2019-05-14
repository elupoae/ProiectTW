<?php
/**
 * User: Nicu Neculache
 * Date: 13.05.2019
 * Time: 20:04
 */

class homeController extends Controller
{
    public function index()
    {
        $params = [];
        $this->model('Account');
        if ($this->model->checkLogin()) {
            $params['login'] = true;
            $params['username'] = $this->model->getUsername();
        } else $params['login'] = false;

        $this->view('home' . DIRECTORY_SEPARATOR . 'index', $params);
        $this->view->render();
    }

    public function login()
    {
        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post") {
            http_response_code(400);
            return;
        }
        $this->model('Account');
        if ($this->model->login($_POST['username'], $_POST['password']))
            $this->view('manager' . DIRECTORY_SEPARATOR . 'index');
        else $this->view('home' . DIRECTORY_SEPARATOR . 'index');
        $this->view->render();
    }

    public function logout()
    {
        $this->model('Account');
        $this->model->logout();
        $this->view('home' . DIRECTORY_SEPARATOR . 'index');
        $this->view->render();
    }
}