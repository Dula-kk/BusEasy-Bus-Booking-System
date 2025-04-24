<?php
include 'Database.php'; // Include Database class

class User {
    private $conn;
    private $role;
    private $firstName;
    private $lastName;
    private $contactNumber;
    private $username;
    private $password;

    public function __construct($firstName, $lastName, $contactNumber, $role, $username, $password) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->contactNumber = $contactNumber;
        $this->role = $role;
        $this->username = $username;
        $this->password = password_hash($password, PASSWORD_DEFAULT); 
        
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getContactNumber() {
        return $this->contactNumber;
    }

    public function getRole() {
        return $this->role;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password; 
    }

    public function register() {
        try {
            $query = "INSERT INTO user (firstname, lastname, contactnumber, username, password, role, created_at)
                      VALUES (:firstname, :lastname, :contactnumber, :username, :password, :role, NOW())";
    
            $stmt = $this->conn->prepare($query);
    
            $stmt->bindParam(':firstname', $this->firstName);
            $stmt->bindParam(':lastname', $this->lastName);
            $stmt->bindParam(':contactnumber', $this->contactNumber);
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':role', $this->role);
    
            $stmt->execute();
            return true; 
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    
    public static function getUserDetailsById($conn, $userId) {
        try {
            $query = "SELECT firstname, lastname, contactnumber, username, role 
                        FROM user 
                        WHERE userid = :userid";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':userid', $userId);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
