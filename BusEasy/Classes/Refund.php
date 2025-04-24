<?php
include 'Database.php';// Include Database class

class Refund {
    private $conn;
    private $refundId;
    private $reservationId; 
    private $status;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function setRefundId($refundId) {
        $this->refundId = $refundId;
    }

    public function getRefundId() {
        return $this->refundId;
    }

    public function setReservationId($reservationId) {
        $this->reservationId = $reservationId;
    }

    public function getReservationId() {
        return $this->reservationId;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getStatus() {
        return $this->status;
    }

    public function createRefund($reservationId) {
        try {
            $query = "INSERT INTO refund (reservationid) 
                        VALUES (:reservation_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':reservation_id', $reservationId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function updateRefundStatus($refundId, $status) {
        try {
            $query = "UPDATE refund SET status = :status WHERE refund_id = :refund_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':refund_id', $refundId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    public function getRefundStatusByReservationId($reservationId) {
        try {
            $query = "SELECT status FROM refund WHERE reservationid = :reservation_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':reservation_id', $reservationId);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['status'] : false;
            
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
}
?>
