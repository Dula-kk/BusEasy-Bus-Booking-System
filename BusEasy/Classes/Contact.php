<?php
require_once 'Database.php'; // Include Database class

class Contact {
    private $contactid;
    private $name;
    private $email;
    private $message;

    private $conn;
    // Constructor
    public function __construct($name = null, $email = null, $message = null) {
        $this->name = $name;
        $this->email = $email;
        $this->message = $message;
        
        $database = new Database();
        $this->conn = $database->connect();
    }


    // Getters and Setters
    public function getContactId() {
        return $this->contactid;
    }

    public function setContactId($contactid) {
        $this->contactid = $contactid;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }


    public function create() {
        $sql = "INSERT INTO contact (name, email, message) VALUES (:name, :email, :message)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindParam(':message', $this->message, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $this->contactid = $this->conn->lastInsertId(); 
            return true;
        } else {
            return false;
        }
    }
}
?>