<?php
class Database {
    private $host = "localhost";
    private $dbname = "BusEasy2";
    private $username = "root";
    private $password = "";
    private $conn;

    public function connect() {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);                //PDO::ATTR_ERRMODE – This attribute controls the error reporting mode. 
                                                                                                     //PDO::ERRMODE_EXCEPTION – This makes PDO throw exceptions when an error occurs.
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return $this->conn;
    }
}
?>
