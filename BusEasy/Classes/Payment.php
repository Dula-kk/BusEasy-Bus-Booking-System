<?php

class Payment {
    private $payment_id;
    private $reservation_id;
    private $amount;
    private $payment_date;
    private $payment_method;
    private $status;
    private $conn;

    public function __construct() {
        
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getPaymentId() {
        return $this->payment_id;
    }

    public function setPaymentId($payment_id) {
        $this->payment_id = $payment_id;
    }

    public function getReservationId() {
        return $this->reservation_id;
    }

    public function setReservationId($reservation_id) {
        $this->reservation_id = $reservation_id;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function setAmount($amount) {
        $this->amount = $amount;
    }

    public function getPaymentDate() {
        return $this->payment_date;
    }

    public function setPaymentDate($payment_date) {
        $this->payment_date = $payment_date;
    }

    public function getPaymentMethod() {
        return $this->payment_method;
    }

    public function setPaymentMethod($payment_method) {
        $this->payment_method = $payment_method;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }


    public function createPayment() {
        try {
            $this->conn->beginTransaction(); 
    
            // Insert payment data
            $query = "INSERT INTO payment (reservationid, amount, payment_method, status) 
                      VALUES (:reservation_id, :amount, :payment_method, 'pending')"; 
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':reservation_id', $this->reservation_id);
            $stmt->bindParam(':amount', $this->amount);
            $stmt->bindParam(':payment_method', $this->payment_method);
            $stmt->execute();
    
            // Update reservation status
            $updateQuery = "UPDATE reservation SET payment_status = 'completed' WHERE reservationid = :reservation_id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':reservation_id', $this->reservation_id);
            $updateStmt->execute();
    
            $this->conn->commit(); 
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack(); 
            return "Error creating payment: " . $e->getMessage();
        }
    }
    
}

?>
