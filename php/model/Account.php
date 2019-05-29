<?php
/**
 * User: Nicu Neculache
 * Date: 14.05.2019
 * Time: 01:06
 */

class Account
{
    /**
     * Account constructor.
     */
    public function __construct()
    {
        if (session_id() == "")
            session_start();
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        if (isset($_SESSION['username']))
            return ucfirst(strtolower($_SESSION['username']));
        else {
            return "Account";
        }
    }

    public function getUserData()
    {
        $conn = Database::getConnection();
        $result = mysqli_query($conn, "SELECT email, last_change FROM users WHERE id='" . $_SESSION['id_user'] . "'");
        $row = mysqli_fetch_array($result);

        $data = [];
        $data['id_user'] = $_SESSION['id_user'];
        $data['username'] = $_SESSION['username'];
        if (is_array($row)) {
            $data['email'] = $row['email'];
            $data['last_change'] = $row['last_change'];
            return $data;
        } else return [];
    }

    public function login($username, $password, $remember = false)
    {
        $conn = Database::getConnection();
        $result = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' and password = '$password'");
        $row = mysqli_fetch_array($result);
        if (is_array($row)) {
            if ($remember) {
                $_COOKIE['id_user'] = $row['id'];
                $_COOKIE['username'] = $username;
            }
            $_SESSION['id_user'] = $row['id'];
            $_SESSION['username'] = $username;
            return true;
        } else return false;
    }

    public function logout()
    {
        unset($_SESSION['id_user']);
        unset($_SESSION['username']);
        unset($_COOKIE['id_user']);
        unset($_COOKIE['username']);
        session_destroy();
    }

    public function checkLogin()
    {
        if (isset($_SESSION['id_user']) && $_SESSION['id_user'] > 0)
            return true;
        elseif (isset($_COOKIE['id_user']) && $_COOKIE['id_user'] > 0) {
            $_SESSION['id_user'] = $_COOKIE['id_user'];
            $_SESSION['username'] = $_COOKIE['username'];
            return true;
        }
        return false;
    }

    public function newAccount($username, $email, $password)
    {
        $conn = Database::getConnection();
        $result = mysqli_query($conn, "INSERT INTO `users` (`id`, `username`, `email`, `password`, `last_change`) 
                                                        VALUES (NULL, '$username', '$email', '$password', CURRENT_DATE());");
        if ($result) {
            $_SESSION['id_user'] = mysqli_insert_id($conn);
            $_SESSION['username'] = $username;
            return true;
        }
        return false;
    }

    public function changePassword($newPassword)
    {
        $conn = Database::getConnection();
        $result = mysqli_query($conn, "UPDATE users SET password = '$newPassword' WHERE id = '" . $_SESSION['id_user'] . "'");

        if ($result && mysqli_affected_rows($conn) == 1)
            return true;
        return false;
    }

    public function search_password($text)
    {
        $conn = Database::getConnection();
        $result = mysqli_query($conn, "SELECT * FROM passwords WHERE id_user='" . $_SESSION['id_user'] . "' AND title LIKE '%$text%' ");
        $passwordList = [];
        while ($row = mysqli_fetch_array($result)) {
            $password = [];
            $password['id'] = $row['id'];
            $password['link'] = $row['link'];
            $password['title'] = $row['title'];
            $password['username'] = $row['username'];
            $password['password'] = $row['password'];
            $password['last_change'] = $row['last_change'];
            array_push($passwordList, $password);
        }
        return $passwordList;
    }

    public function get_passwords()
    {
        $conn = Database::getConnection();
        $result = mysqli_query($conn, "SELECT * FROM passwords WHERE id_user='" . $_SESSION['id_user'] . "'");
        $passwordList = [];
        while ($row = mysqli_fetch_array($result)) {
            $password = [];
            $password['id'] = $row['id'];
            $password['link'] = $row['link'];
            $password['title'] = $row['title'];
            $password['username'] = $row['username'];
            $password['password'] = $row['password'];
            $password['last_change'] = $row['last_change'];
            array_push($passwordList, $password);
        }
        return $passwordList;
    }

    public function new_password($link, $username, $password)
    {
        $title = str_replace("www.", "", parse_url($link, PHP_URL_HOST));

        $conn = Database::getConnection();
        $result = mysqli_query($conn, "INSERT INTO `passwords` (`id`, `id_user`, `link`, `title`, `username`, `password`, `last_change`) 
            VALUES (NULL, '" . $_SESSION['id_user'] . "', '$link', '$title', '$username', '$password', CURRENT_DATE());");

        if ($result)
            return true;
        return false;
    }

    public function edit_password($id, $username, $password)
    {
        $conn = Database::getConnection();
        $result = mysqli_query($conn, "UPDATE passwords SET username = '$username', password = '$password', last_change = CURRENT_DATE() WHERE id = $id;");

        if ($result && mysqli_affected_rows($conn) == 1)
            return true;
        return false;
    }

    public function delete_password($id)
    {
        $conn = Database::getConnection();

        if (mysqli_query($conn, "DELETE FROM passwords WHERE id = $id"))
            return true;
        return false;
    }

    public static function statistic($passwordList)
    {
        $statistic = [];
        $uniq_passwords = [];
        $repeated_passwords = [];
        $current_date = new DateTime("now");

        $statistic['password_count'] = count($passwordList);
        $statistic['expired_passwords'] = 0;
        $statistic['date'] = [];
        foreach ($passwordList as $password) {
            $last_change = new DateTime($password['last_change']);
            $interval = $current_date->diff($last_change, true);
            if (($interval->m + 12 * $interval->y) > 3)
                $statistic['expired_passwords']++;
            if (!in_array($password['password'], $uniq_passwords)) {
                array_push($uniq_passwords, $password['password']);
            } else {
                if (!in_array($password['password'], $repeated_passwords))
                    array_push($repeated_passwords, $password['password']);
            }
        }
        $statistic['identical_passwords'] = count($passwordList) - count($uniq_passwords) + count($repeated_passwords);
        return $statistic;
    }
}