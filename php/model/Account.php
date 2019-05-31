<?php
/**
 * User: Nicu Neculache
 * Date: 14.05.2019
 * Time: 01:06
 */

class Account
{
    public static $k = "D122EB921D2B21FD133C9B98C9A6E";

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
        try {
            $conn = Database::getConnection();
            $stmt = mysqli_prepare($conn, "SELECT email, last_change FROM users WHERE id=? LIMIT 1");
            $stmt->bind_param("i", $_SESSION['id_user']);
            if (!$stmt->execute())
                throw new mysqli_sql_exception($stmt->error, $stmt->errno);
            $stmt->store_result();
            if ($stmt->store_result() || $stmt->num_rows == 0) {
                $stmt->close();
                $this->logout();
                Application::logger("User with id " . $_SESSION['id_user'] . " don't exist in database", __METHOD__);
                Application::redirectTo("/home/info_page");
                return [];
            }
            $stmt->bind_result($email, $last_change);
            $stmt->fetch();

            $data = [];
            $data['id_user'] = $_SESSION['id_user'];
            $data['username'] = $_SESSION['username'];
            $data['email'] = $email;
            $data['last_change'] = $last_change;
            $stmt->close();
            return $data;
        } catch (mysqli_sql_exception $sql_exception) {
            Application::logger("code: " . $sql_exception->getCode() . " message:" . $sql_exception->getMessage(), __METHOD__, "SQL_EXCEPTION");
        } catch (Exception $exception) {
            Application::logger("code: " . $exception->getCode() . " message:" . $exception->getMessage(), __METHOD__, "EXCEPTION");
        }
        $this->logout();
        Application::redirectTo("/home/internal_error");
        return [];
    }

    public function login($username, $password, $remember = false)
    {
        try {
            $conn = Database::getConnection();
            $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username= ? and password = ?");
            $stmt->bind_param("ss", $username, crypt($password, self::$k));
            if (!$stmt->execute())
                throw new mysqli_sql_exception($stmt->error, $stmt->errno);
            $stmt->store_result();
            if ($stmt->num_rows == 0) {
                $stmt->close();
                return false;
            }
            $stmt->bind_result($id);
            $stmt->fetch();

            if ($remember) {
                $_COOKIE['id_user'] = $id;
                $_COOKIE['username'] = $username;
            }
            $_SESSION['id_user'] = $id;
            $_SESSION['username'] = $username;
            $stmt->close();
            return true;
        } catch (mysqli_sql_exception $sql_exception) {
            Application::logger("code: " . $sql_exception->getCode() . " message:" . $sql_exception->getMessage(), __METHOD__, "SQL_EXCEPTION");
        } catch (Exception $exception) {
            Application::logger("code: " . $exception->getCode() . " message:" . $exception->getMessage(), __METHOD__, "EXCEPTION");
        }
        Application::redirectTo("/home/internal_error");
        return false;
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
        try {
            $conn = Database::getConnection();
            $stmt = mysqli_prepare($conn, "INSERT INTO users VALUES (NULL, ?, ?, ?, CURRENT_DATE());");
            $crypt = self::encrypt($password);
            $stmt->bind_param("sss", $username, $email, crypt($password, self::$k));

            if (!$stmt->execute() || $stmt->affected_rows == 0)
                throw new mysqli_sql_exception($stmt->error, $stmt->errno);
            $_SESSION['id_user'] = $stmt->insert_id;
            $_SESSION['username'] = $username;
            $_SESSION['message'] = "Thank you for choosing us";
            $stmt->close();
            return true;
        } catch (mysqli_sql_exception $sql_exception) {
            if ($sql_exception->getCode() == 1062) {
                if (strpos($sql_exception->getMessage(), "username") != false)
                    $_SESSION['message'] = "Username already exist";
                else $_SESSION['message'] = "Email already exist";
                $_SESSION['message_color'] = "warning";
                return false;
            }
            Application::logger("code: " . $sql_exception->getCode() . " message:" . $sql_exception->getMessage(), __METHOD__, "SQL_EXCEPTION");
        } catch (Exception $exception) {
            Application::logger("code: " . $exception->getCode() . " message:" . $exception->getMessage(), __METHOD__, "EXCEPTION");
        }
        $_SESSION['message'] = "An error occurred, please try later.";
        $_SESSION['message_color'] = "error";
        return false;
    }

    public function changePassword($newPassword)
    {
        try {
            $conn = Database::getConnection();
            $stmt = mysqli_prepare($conn, "UPDATE users SET password = ?, last_change = CURRENT_DATE() WHERE id = ?");
            $stmt->bind_param("si", crypt($newPassword, self::$k), $_SESSION['id_user']);

            if (!$stmt->execute() || $stmt->affected_rows == 0)
                throw new mysqli_sql_exception($stmt->error, $stmt->errno);
            $stmt->close();
            $_SESSION['message'] = "Your MAIN password has been successfully updated.";
            return;
        } catch (mysqli_sql_exception $sql_exception) {
            Application::logger("code: " . $sql_exception->getCode() . " message:" . $sql_exception->getMessage(), __METHOD__, "SQL_EXCEPTION");
        } catch (Exception $exception) {
            Application::logger("code: " . $exception->getCode() . " message:" . $exception->getMessage(), __METHOD__, "EXCEPTION");
        }
        $_SESSION['message'] = "An error occurred, your MAIN password has NOT been updated.";
        $_SESSION['message_color'] = "error";
    }

    public function search_password($text)
    {
        try {
            $passwordList = [];
            $text = "%$text%";
            $conn = Database::getConnection();
            $stmt = mysqli_prepare($conn, "SELECT * FROM passwords WHERE id_user= ? AND title LIKE ?");
            $stmt->bind_param("is", $_SESSION['id_user'], $text);
            if (!$stmt->execute())
                throw new mysqli_sql_exception($stmt->error, $stmt->errno);
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $password = [];
                $password['id'] = $row['id'];
                $password['link'] = $row['link'];
                $password['title'] = $row['title'];
                $password['username'] = $row['username'];
                $password['password'] = self::decrypt($row['password'], $row['iv']);
                $password['last_change'] = $row['last_change'];
                array_push($passwordList, $password);
            }
            $stmt->close();
            return $passwordList;
        } catch (mysqli_sql_exception $sql_exception) {
            Application::logger("code: " . $sql_exception->getCode() . " message:" . $sql_exception->getMessage(), __METHOD__, "SQL_EXCEPTION");
        } catch (Exception $exception) {
            Application::logger("code: " . $exception->getCode() . " message:" . $exception->getMessage(), __METHOD__, "EXCEPTION");
        }
        $_SESSION['message'] = "An error occurred.";
        $_SESSION['message_color'] = "error";
        return [];
    }

    public function get_passwords()
    {
        try {
            $passwordList = [];
            $conn = Database::getConnection();
            $stmt = mysqli_prepare($conn, "SELECT * FROM passwords WHERE id_user= ?");
            $stmt->bind_param("i", $_SESSION['id_user']);
            if (!$stmt->execute())
                throw new mysqli_sql_exception($stmt->error, $stmt->errno);
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $password = [];
                $password['id'] = $row['id'];
                $password['link'] = $row['link'];
                $password['title'] = $row['title'];
                $password['username'] = $row['username'];
                $password['password'] = self::decrypt($row['password'], $row['iv']);
                $password['last_change'] = $row['last_change'];
                array_push($passwordList, $password);
            }
            $stmt->close();
            return $passwordList;
        } catch (mysqli_sql_exception $sql_exception) {
            Application::logger("code: " . $sql_exception->getCode() . " message:" . $sql_exception->getMessage(), __METHOD__, "SQL_EXCEPTION");
        } catch (Exception $exception) {
            Application::logger("code: " . $exception->getCode() . " message:" . $exception->getMessage(), __METHOD__, "EXCEPTION");
        }
        $_SESSION['message'] = "An error occurred.";
        $_SESSION['message_color'] = "error";
        return [];
    }

    public function new_password($link, $username, $password)
    {
        try {
            $title = str_replace("www.", "", parse_url($link, PHP_URL_HOST));

            $conn = Database::getConnection();
            $stmt = mysqli_prepare($conn, "INSERT INTO passwords VALUES (NULL, ?, ?, ?, ?, ?, CURRENT_DATE(), ?);");
            $crypt = self::encrypt($password);
            $stmt->bind_param("isssss", $_SESSION['id_user'], $link, $title, $username, $crypt['hash'], $crypt['iv']);

            if (!$stmt->execute() || $stmt->affected_rows == 0)
                throw new mysqli_sql_exception($stmt->error, $stmt->errno);
            $stmt->close();
            $_SESSION['message'] = "Your password has been successfully saved.";
            return;
        } catch (mysqli_sql_exception $sql_exception) {
            Application::logger("code: " . $sql_exception->getCode() . " message:" . $sql_exception->getMessage(), __METHOD__, "SQL_EXCEPTION");
        } catch (Exception $exception) {
            Application::logger("code: " . $exception->getCode() . " message:" . $exception->getMessage(), __METHOD__, "EXCEPTION");
        }
        $_SESSION['message'] = "An error occurred, your password has NOT been saved.";
        $_SESSION['message_color'] = "error";
    }

    public function edit_password($id, $username, $password)
    {
        try {
            $conn = Database::getConnection();
            $stmt = mysqli_prepare($conn, "UPDATE passwords SET username = ?, password = ?, last_change = CURRENT_DATE(), iv = ? WHERE id = ?");
            $crypt = self::encrypt($password);
            $stmt->bind_param("sssi", $username, $crypt['hash'], $crypt['iv'], $id);

            if (!$stmt->execute() || $stmt->affected_rows == 0)
                throw new mysqli_sql_exception($stmt->error, $stmt->errno);
            $stmt->close();
            $_SESSION['message'] = "Your password has been successfully updated.";
            return;
        } catch (mysqli_sql_exception $sql_exception) {
            Application::logger("code: " . $sql_exception->getCode() . " message:" . $sql_exception->getMessage(), __METHOD__, "SQL_EXCEPTION");
        } catch (Exception $exception) {
            Application::logger("code: " . $exception->getCode() . " message:" . $exception->getMessage(), __METHOD__, "EXCEPTION");
        }
        $_SESSION['message'] = "An error occurred, your password has NOT been updated.";
        $_SESSION['message_color'] = "error";
    }

    public function delete_password($id)
    {
        try {
            $conn = Database::getConnection();
            $stmt = mysqli_prepare($conn, "DELETE FROM passwords WHERE id = ?");
            $stmt->bind_param("i", $id);
            if (!$stmt->execute() || $stmt->affected_rows == 0)
                throw new mysqli_sql_exception($stmt->error, $stmt->errno);
            $stmt->close();
            $_SESSION['message'] = "Your password has been successfully deleted.";
            return;
        } catch (mysqli_sql_exception $sql_exception) {
            Application::logger("code: " . $sql_exception->getCode() . " message:" . $sql_exception->getMessage(), __METHOD__, "SQL_EXCEPTION");
        } catch (Exception $exception) {
            Application::logger("code: " . $exception->getCode() . " message:" . $exception->getMessage(), __METHOD__, "EXCEPTION");
        }
        $_SESSION['message'] = "An error occurred, your password has NOT been deleted.";
        $_SESSION['message_color'] = "error";
    }

    public static function statistic($passwordList)
    {
        $statistic = [];
        $uniq_passwords = [];
        $repeated_passwords = [];
        $current_date = new DateTime("now");

        $statistic['password_count'] = count($passwordList);
        $statistic['expired_passwords'] = 0;
        $statistic['weak_passwords'] = 0;
        foreach ($passwordList as $password) {
            // week password
            if (self::is_week($password['password']))
                $statistic['weak_passwords']++;
            // expired password
            try {
                $last_change = new DateTime($password['last_change']);
                $interval = $current_date->diff($last_change, true);
                if (($interval->m + 12 * $interval->y) > 3)
                    $statistic['expired_passwords']++;
            } catch (Exception $exception) {
                Application::logger("code: " . $exception->getCode() . " message:" . $exception->getMessage(), __METHOD__, "EXCEPTION");
            }
            // uniq password
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

    private static function is_week($password)
    {
        $valid_check = 0;

        if (strlen($password) > 14) {
            $valid_check += 40;
        } else if (strlen($password) > 10) {
            $valid_check += 20;
        } else if (strlen($password) > 7) {
            $valid_check += 10;
        } else if (strlen($password) < 7) {
            $valid_check = 5;
        }

        if ($valid_check > 10)
            return false;
        else return true;
    }

    private static function encrypt($password)
    {
        $method = 'AES-256-CBC';
        $iv_len = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($iv_len);
        return array("hash" => openssl_encrypt($password, $method, self::$k, 0, $iv), "iv" => $iv);
    }

    private static function decrypt($crypt, $iv)
    {
        $method = 'AES-256-CBC';
        return openssl_decrypt($crypt, $method, self::$k, 0, $iv);
    }
}
