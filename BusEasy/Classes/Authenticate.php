<?php

require_once 'Database.php'; // Include Database class
class Authenticate {
    private $conn;
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function login($username, $password) {
        $query = "SELECT userid, username, password, role FROM user WHERE username = :username LIMIT 1"; // placeholder = ? =:username
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username); //:username 1
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user_data['password'])) {
                return $user_data;
            } else {
                return false;
            }
        }
        return false;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function logout() {
        session_destroy();
        unset($_SESSION);
        setcookie('userid', '', time()-60, '/');
        setcookie('username', '', time()-60, '/');
        setcookie('role', '', time()-60, '/');
    }
}
/* $query = "UPDATE users SET email = :email WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':email', $newEmail);
$stmt->bindParam(':id', $userId);
$stmt->execute(); */

?>
