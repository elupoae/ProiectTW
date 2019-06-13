<?php

class accountController extends Controller
{
    public function index($page = 1)
    {
        $params = [];
        $this->model('Account');
        if (!$this->model->checkLogin())
            Application::redirectTo();
        $params['username'] = $this->model->getUsername();
        $params['passwords'] = $this->model->get_passwords($page);
        $params['count_page'] = count($params['passwords']);
        $params['current_page'] = $page;
        $params = array_merge($params, Account::statistic());
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
        $this->model('Account');
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
        $this->model('Account');
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

        $this->model('Account');

        if ($this->model->newAccount($_POST['username'], $_POST['email'], $_POST['password']))
            Application::redirectTo("/account");
        Application::redirectTo("/account/register");
    }

    public function changePassword()
    {
        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post" || empty($_POST['password'])
            || empty($_POST['confirmPassword']) || $_POST['confirmPassword'] != $_POST['password'])
            $this->bad_request();

        $this->model('Account');
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
        $this->model('Account');
        if (!$this->model->checkLogin())
            Application::redirectTo();
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

        $this->model('Account');
        $this->model->new_password($_POST['link'], $_POST['username'], $_POST['password']);
        Application::redirectTo("/account");
    }

    public function editPassword($id)
    {
        if (!is_string($id) || strlen(intval($id)) != strlen($id))
            Application::redirectTo("/account");

        if (strtolower($_SERVER["REQUEST_METHOD"]) != "post" || empty($_POST['username']) || empty($_POST['password']))
            $this->bad_request();

        $this->model('Account');
        $this->model->edit_password($id, $_POST['username'], $_POST['password']);
        Application::redirectTo("/account");
    }

    public function deletePassword($id)
    {
        if (!is_string($id) || strlen(intval($id)) != strlen($id))
            Application::redirectTo("/account");

        $this->model('Account');
        $this->model->delete_password($id);
        Application::redirectTo("/account");
    }

    public function export_json($search = "")
    {
        $this->model('Account');
        if (!$this->model->checkLogin())
            Application::redirectTo();
        $params = [];
        $params['username'] = $this->model->getUsername();
        if ($search != "") $params['passwords'] = $this->model->search_password($search);
        else $params['passwords'] = $this->model->get_passwords(0);
        $params = array_merge(Account::statistic($params['passwords']), $params);
        header('Content-Type: application/json');
        echo json_encode($params);
    }

    public function export_xml($search = "")
    {
        $this->model('Account');
        if (!$this->model->checkLogin())
            Application::redirectTo();
        $params = [];
        $params['username'] = $this->model->getUsername();
        if ($search != "") $params['passwords'] = $this->model->search_password($search);
        else $params['passwords'] = $this->model->get_passwords(0);
        $params = array_merge(Account::statistic($params['passwords']), $params);
        header('Content-Type: application/xml');

        $xml = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
        $this->array_to_xml($xml, $params);
        echo $xml->asXML();
    }

    private function array_to_xml(&$xml, $data)
    {
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $key = 'item' . $key;
            }
            if (is_array($value)) {
                $sub_node = $xml->addChild($key);
                $this->array_to_xml($sub_node, $value);
            } else {
                $xml->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }

    public function export_csv($search = "")
    {
        $this->model('Account');
        if (!$this->model->checkLogin())
            Application::redirectTo();
        $params = [];
        $params['username'] = $this->model->getUsername();
        if ($search != "") $params['passwords'] = $this->model->search_password($search);
        else $params['passwords'] = $this->model->get_passwords(0);
        $params = array_merge(Account::statistic($params['passwords']), $params);
        header("Content-Type:application/csv");
        header("Content-Disposition:attachment;filename=passwords.csv");
        $output = fopen("php://output",'w');
        fputcsv($output, array('id','link','title','username','password','last_change'));
        foreach($params['passwords'] as $password) {
            fputcsv($output, $password);
        }
        fclose($output);
    }
}
