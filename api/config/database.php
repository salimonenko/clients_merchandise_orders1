<?php

class Database{

    // specify own database credentials
    private $host = "localhost";
    private $db_name = "clients_merchandise_orders";
    private $username = "root";
    private $password = "";
    public $conn;

    // get the database connection
    public function getConnection(){

        $this->conn = null;

        $dsn_Options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password, $dsn_Options);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
