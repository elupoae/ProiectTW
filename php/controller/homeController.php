<?php

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
        if (isset($_SESSION['message'])) {
            $params['message'] = $_SESSION['message'];
            unset($_SESSION['message']);
            if (isset($_SESSION['message_color'])) {
                $params['message_color'] = $_SESSION['message_color'];
                unset($_SESSION['message_color']);
            }
        }

        $this->view('home' . DIRECTORY_SEPARATOR . 'index', $params);
        $this->view->render();
    }

    public function login()
    {
        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post" || empty($_POST['username']) || empty($_POST['password']) || !ctype_alnum($_POST['username']))
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
        $_SESSION['message'] = "Logout successfully.";
        Application::redirectTo();
    }

    public function contact()
    {
        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post" || empty($_POST['subject']) || empty($_POST['message']) || !ctype_alnum($_POST['subject']))
            $this->bad_request();



        $host = "smtp.gmail.com";
        $username = "youremail@example.com";
        $password = "your email password";
        $port = "465";

        $to = "nnicu8@gmail.com";
        $subject = $_POST['subject'];
        $from = "no-reply@maxlock.com";
        $body = $_POST['message'];

        $date = date("Y-m-d h:m:s");
        $contact = json_encode(array('date' => $date, 'subject' => $subject, 'message' => $body));

        $contact_file = ROOT . 'log/contact.log';
        error_log($contact, 3, $contact_file);

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

    public function internal_error()
    {
        http_response_code(500);
        $params = array();
        $params['title'] = "Error 500";
        $params['content'] = "Internal Server Error";
        $this->view('home' . DIRECTORY_SEPARATOR . 'infoPage', $params);
        $this->view->render();
    }
}
