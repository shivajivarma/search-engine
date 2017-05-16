<?php

class MySQL
{
    public $conn;

    //Establishing connection to database
    function __construct()
    {
        require_once 'constants.php';
        $this->conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
        if (mysqli_connect_errno()) {
            die('die-error: Could not connect to database:');
        }
    }


    function crawlerPrint()
    {
        $result = mysqli_query($this->conn, "SELECT * FROM crawler where print=0");

        while ($row = mysqli_fetch_array($result)) {
            echo $row['id'] . " " . $row['url'];
            echo "<br>";
        }
        mysqli_query($this->conn, "UPDATE crawler SET print=1 WHERE print=0");
    }



    function countLinks()
    {
        $result = mysqli_query($this->conn, "SELECT COUNT(*) as c FROM crawler where ftch=1");
        $row = mysqli_fetch_array($result);
        echo "Number of links collected:" . $row['c'] . "<br>";
    }


    function __destruct()
    {
        mysqli_close($this->conn);
    }
}

?>
