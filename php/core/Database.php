<?php
/**
 * User: Nicu Neculache
 * Date: 14.05.2019
 * Time: 01:12
 */

//error_reporting(E_ALL);
//ini_set("display_errors", "On");

class Database
{
    const HOST = "127.0.0.1:3306";
    const USER = "root";
    const PASSWORD = "";
    const DATABASE = "maxlock";
    private static $conn = null;

    public static function setConnection()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            self::$conn = new mysqli(self::HOST, self::USER, self::PASSWORD, self::DATABASE);
        } catch (mysqli_sql_exception $sql_exception) {
            $conn = null;
            Application::logger("code: " . $sql_exception->getCode() . " message:" . $sql_exception->getMessage(), __CLASS__, "SQL_EXCEPTION");
        }
    }

    public static function getConnection()
    {
        if(self::$conn == null)
            throw new Exception("Database connection is null");
        return self::$conn;
    }
}