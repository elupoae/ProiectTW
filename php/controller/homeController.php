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
        } else {
            $params['login'] = false;
            if (isset($_SESSION['login_failed'])) {
                $params['login_failed'] = true;
                unset($_SESSION['login_failed']);
            }
        }

        $this->view('home' . DIRECTORY_SEPARATOR . 'index', $params);
        $this->view->render();
    }

    public function login()
    {
        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post" || !isset($_POST['username']) || !isset($_POST['password']) || !ctype_alnum($_POST['username']))
            $this->bad_request();
        $this->model('Account');

        if ($this->model->login($_POST['username'], $_POST['password'], isset($_POST['remember'])))
            Application::redirectTo("/account");
        else {
            $_SESSION['login_failed'] = true;
            Application::redirectTo();
        }
    }

    public function logout()
    {
        $this->model('Account');
        $this->model->logout();
        Application::redirectTo();
    }

    public function contact()
    {
        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post" || !isset($_POST['subject']) || !isset($_POST['message']) || !ctype_alnum($_POST['subject'])) {
            http_response_code(400);
            return;
        }

        $params = [];
        $params['title'] = "Thank you!";
        $params['content'] = "The message was successfully sent.";
        $this->view('home' . DIRECTORY_SEPARATOR . 'infoPage', $params);
        $this->view->render();
    }

    public function infoPage($title = "Error", $content = "An%20error%20has%20been%20encountered")
    {
        $params = [];
        $params['title'] = str_replace("%20", " ", $title);
        $params['content'] = str_replace("%20", " ", $content);
        $this->view('home' . DIRECTORY_SEPARATOR . 'infoPage', $params);
        $this->view->render();
    }
}