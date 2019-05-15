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
        $params['password_count'] = count($params['passwords']);
        $params['identical_password'] = $this->identical_passwords($params['passwords']);
        if (isset($_SESSION['message'])) {
            $params['message'] = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        $this->view('account' . DIRECTORY_SEPARATOR . 'index', $params);
        $this->view->render();
    }

    public function register()
    {
        $this->view('account' . DIRECTORY_SEPARATOR . 'register');
        $this->view->render();
    }

    public function settings()
    {
        $this->model('Account');
        if (!$this->model->checkLogin())
            Application::redirectTo();

        $params = $this->model->getUserData();

        $this->view('account' . DIRECTORY_SEPARATOR . 'settings', $params);
        $this->view->render();
    }

    public function newAccount()
    {
        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post") {
            http_response_code(400);
            return;
        }
        if ($_POST['confirmPassword'] != $_POST['password'])
            return;

        $this->model('Account');

        $this->model->newAccount($_POST['username'], $_POST['email'], $_POST['password']);
        Application::redirectTo("/account");
    }

    public function changePassword()
    {
        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post") {
            http_response_code(400);
            return;
        }
        if ($_POST['confirmPassword'] != $_POST['password'] || empty($_POST['password'])) {
            Application::redirectTo("/account/settings");
            return;
        }

        $this->model('Account');

        if ($this->model->changePassword($_POST['password']))
            $_SESSION['message'] = "Your MAIN password has been successfully updated.";
        else $_SESSION['message'] = "Your MAIN password has NOT been updated.";
        Application::redirectTo("/account");
    }

    public function newPassword()
    {
        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post") {
            http_response_code(400);
            return;
        }
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

        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post") {
            http_response_code(400);
            return;
        }
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

    private function identical_passwords($passwordList)
    {
        $uniq_passwords = [];
        $repeated_passwords = [];
        foreach ($passwordList as $password) {
            if (!in_array($password['password'], $uniq_passwords)) {
                array_push($uniq_passwords, $password['password']);
            } else {
                if (!in_array($password['password'], $repeated_passwords))
                    array_push($repeated_passwords, $password['password']);
            }
        }
        return count($passwordList) - count($uniq_passwords) + count($repeated_passwords);
    }
}