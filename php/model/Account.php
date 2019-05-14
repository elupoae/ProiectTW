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
        session_start();
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        if (isset($_SESSION['username']))
            return $_SESSION['username'];
        else return "Account";
    }

    public function login($username, $password)
    {
        $conn = Database::getConnection();
        $result = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' and password = '$password'");
        $row = mysqli_fetch_array($result);
        if (is_array($row)) {
            $_SESSION['id_user'] = $row['id'];
            $_SESSION['username'] = $username;
            return true;
        } else return false;
    }

    public function logout()
    {
        $_SESSION['id_user'] = "";
        session_destroy();
    }

    public function checkLogin()
    {
        if (isset($_SESSION['id_user']) && $_SESSION['id_user'] > 0)
            return true;
        return false;
    }
}