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
        $this->model('Account');
        if (!$this->model->checkLogin())
            Application::redirectTo();

        $params['username'] = $this->model->getUsername();
        $params['passwords'] = $this->model->get_passwords();
        $params = array_merge($params, Account::statistic($params['passwords']));
        if (isset($_SESSION['message'])) {
            $params['message'] = $_SESSION['message'];
            unset($_SESSION['message']);
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
        }
        $this->view('account' . DIRECTORY_SEPARATOR . 'register', $params);
        $this->view->render();
    }

    public function settings()
    {
        $this->model('Account');
        if (!$this->model->checkLogin())
            Application::redirectTo();

        $params = $this->model->getUserData();
        if (isset($_SESSION['message'])) {
            $params['message'] = $_SESSION['message'];
            unset($_SESSION['message']);
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

        $this->model('Account');

        if ($this->model->newAccount($_POST['username'], $_POST['email'], $_POST['password']))
            Application::redirectTo("/account");
        $_SESSION['message'] = "Something is not working well, retry later.";
        Application::redirectTo("/account/register");
    }

    public function changePassword()
    {
        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post" || empty($_POST['password'])
            || empty($_POST['confirmPassword']) || $_POST['confirmPassword'] != $_POST['password'])
            $this->bad_request();

        $this->model('Account');

        if ($this->model->changePassword($_POST['password']))
            $_SESSION['message'] = "Your MAIN password has been successfully updated.";
        else $_SESSION['message'] = "Your MAIN password has NOT been updated.";
        Application::redirectTo("/account/settings");
    }

    public function search()
    {
        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post" || !isset($_POST['search']))
            $this->bad_request();
        if(empty($_POST['search']))
        {
            $this->index();
            return;
        }
        $params = [];
        $this->model('Account');

        $params['search'] = $_POST['search'];
        $params['username'] = $this->model->getUsername();
        $params['passwords'] = $this->model->search_password($_POST['search']);
        $params = array_merge($params, Account::statistic($params['passwords']));
        if (isset($_SESSION['message'])) {
            $params['message'] = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        $this->view('account' . DIRECTORY_SEPARATOR . 'index', $params);
        $this->view->render();
    }

    public function newPassword()
    {
        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post" || empty($_POST['link']) || empty($_POST['username']) || empty($_POST['password']))
            $this->bad_request();
        $this->model('Account');

        if ($this->model->new_password($_POST['link'], $_POST['username'], $_POST['password']))
            $_SESSION['message'] = "Your password has been successfully saved.";
        else $_SESSION['message'] = "Your password has NOT been saved.";
        Application::redirectTo("/account");
    }

    public function editPassword($id)
    {
        if (!is_string($id) || strlen(intval($id)) != strlen($id))
            Application::redirectTo("/account");

        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post" || empty($_POST['username']) || empty($_POST['password']))
            $this->bad_request();
        $this->model('Account');

        if ($this->model->edit_password($id, $_POST['username'], $_POST['password']))
            $_SESSION['message'] = "Your password has been successfully updated.";
        else $_SESSION['message'] = "Your password has NOT been updated.";
        Application::redirectTo("/account");
    }

    public function deletePassword($id)
    {
        if (!is_string($id) || strlen(intval($id)) != strlen($id))
            Application::redirectTo("/account");

        $this->model('Account');

        if ($this->model->delete_password($id))
            $_SESSION['message'] = "Your password has been successfully deleted.";
        else $_SESSION['message'] = "Your password has NOT been deleted.";
        Application::redirectTo("/account");
    }
}