<?php
/**
 * User: Nicu Neculache
 * Date: 13.05.2019
 * Time: 23:51
 */

class accountController extends Controller
{
    public function index()
    {
        $params = [];
        $this->model = new Account();
        if (!$this->model->checkLogin())
            Application::redirectTo();
        $params['username'] = $this->model->getUsername();
        $params['passwords'] = $this->model->get_passwords();
        $params = array_merge($params, Account::statistic($params['passwords']));
        if (isset($_SESSION['message'])) {
            $params['message'] = $_SESSION['message'];
            unset($_SESSION['message']);
            if (isset($_SESSION['message_color'])) {
                $params['message_color'] = $_SESSION['message_color'];
                unset($_SESSION['message_color']);
            }
        }

        $this->view('account' . DIRECTORY_SEPARATOR . 'index', $params);
        $this->view->render();
    }

    public function register()
    {
        $params = array();
        if (isset($_SESSION['message'])) {
            $params['message'] = $_SESSION['message'];
            unset($_SESSION['message']);
            if (isset($_SESSION['message_color'])) {
                $params['message_color'] = $_SESSION['message_color'];
                unset($_SESSION['message_color']);
            }
        }
        $this->view('account' . DIRECTORY_SEPARATOR . 'register', $params);
        $this->view->render();
    }

    public function settings()
    {
        $this->model = new Account();
        if (!$this->model->checkLogin())
            Application::redirectTo();

        $params = $this->model->getUserData();
        if (isset($_SESSION['message'])) {
            $params['message'] = $_SESSION['message'];
            unset($_SESSION['message']);
            if (isset($_SESSION['message_color'])) {
                $params['message_color'] = $_SESSION['message_color'];
                unset($_SESSION['message_color']);
            }
        }

        $this->view('account' . DIRECTORY_SEPARATOR . 'settings', $params);
        $this->view->render();
    }

    public function newAccount()
    {
        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post" || empty($_POST['username'])
            || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirmPassword'])
            || $_POST['confirmPassword'] != $_POST['password'])
            $this->bad_request();

        $this->model = new Account();

        if ($this->model->newAccount($_POST['username'], $_POST['email'], $_POST['password']))
            Application::redirectTo("/account");
        Application::redirectTo("/account/register");
    }

    public function changePassword()
    {
        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post" || empty($_POST['password'])
            || empty($_POST['confirmPassword']) || $_POST['confirmPassword'] != $_POST['password'])
            $this->bad_request();

        $this->model = new Account();
        $this->model->changePassword($_POST['password']);
        Application::redirectTo("/account/settings");
    }

    public function search()
    {
        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post" || !isset($_POST['search']))
            $this->bad_request();
        if (empty($_POST['search'])) {
            Application::redirectTo("/account");
        }
        $this->model = new Account();
        $params = [];
        $params['search'] = $_POST['search'];
        $params['username'] = $this->model->getUsername();
        $params['passwords'] = $this->model->search_password($_POST['search']);
        $params = array_merge($params, Account::statistic($params['passwords']));
        if (isset($_SESSION['message'])) {
            $params['message'] = $_SESSION['message'];
            unset($_SESSION['message']);
            if (isset($_SESSION['message_color'])) {
                $params['message_color'] = $_SESSION['message_color'];
                unset($_SESSION['message_color']);
            }
        }
        $this->view('account' . DIRECTORY_SEPARATOR . 'index', $params);
        $this->view->render();
    }

    public function newPassword()
    {
        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post" || empty($_POST['link']) || empty($_POST['username']) || empty($_POST['password']))
            $this->bad_request();

        $this->model = new Account();
        $this->model->new_password($_POST['link'], $_POST['username'], $_POST['password']);
        Application::redirectTo("/account");
    }

    public function editPassword($id)
    {
        if (!is_string($id) || strlen(intval($id)) != strlen($id))
            Application::redirectTo("/account");

        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post" || empty($_POST['username']) || empty($_POST['password']))
            $this->bad_request();

        $this->model = new Account();
        $this->model->edit_password($id, $_POST['username'], $_POST['password']);
        Application::redirectTo("/account");
    }

    public function deletePassword($id)
    {
        if (!is_string($id) || strlen(intval($id)) != strlen($id))
            Application::redirectTo("/account");

        $this->model = new Account();
        $this->model->delete_password($id);
        Application::redirectTo("/account");
    }
}