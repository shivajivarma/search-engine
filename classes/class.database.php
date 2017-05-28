<?php
include_once(dirname(__DIR__) . '\classes\constants.php');
include_once(dirname(__DIR__) . '\classes\CustomException.php');

/*
* Mysql database class - only one connection allowed
*/

class Database
{
    private $_connection;
    private static $_instance; //The single instance
    private $_host = DB_SERVER;
    private $_username = DB_USER;
    private $_password = DB_PASSWORD;
    private $_database = DB_NAME;

    /*
    Get an instance of the Database
    @return Instance
    */
    public static function getInstance()
    {
        if (!self::$_instance) { // If no instance then make one
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    // Constructor
    private function __construct()
    {
        $this->_connection = new mysqli($this->_host, $this->_username,
            $this->_password, $this->_database);

        // Error handling

        if (mysqli_connect_error()) {
            throw new CustomException("Failed to connect to MySQL: " . mysqli_connect_error(), 500);
        }
    }

    // Magic method clone is empty to prevent duplication of connection
    private function __clone()
    {
    }

    // Get mysqli connection
    public function getConnection()
    {
        return $this->_connection;
    }
}

?>