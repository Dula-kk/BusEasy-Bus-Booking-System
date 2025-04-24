<?php
class Feedback {
    private $feedback_id;
    private $reservation_id;
    private $message;

    // Setters
    public function setFeedbackId($feedback_id) {
        $this->feedback_id = $feedback_id;
    }

    public function setReservationId($reservation_id) {
        $this->reservation_id = $reservation_id;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    // Getters
    public function getFeedbackId() {
        return $this->feedback_id;
    }

    public function getReservationId() {
        return $this->reservation_id;
    }

    public function getMessage() {
        return $this->message;
    }

    public function createFeedback($conn) {
        // Log the reservation ID
        error_log("Reservation ID in createFeedback: " . $this->reservation_id);
    
        // Check if the reservation ID exists in the reservation table
        $checkSql = "SELECT reservationid FROM reservation WHERE reservationid = :reservation_id";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(":reservation_id", $this->reservation_id);
        $checkStmt->execute();
    
        if ($checkStmt->rowCount() == 0) {
            // Log if reservation ID does not exist
            error_log("Reservation ID does not exist: " . $this->reservation_id);
            return "Error: Reservation ID does not exist.";
        }
    
        // Proceed with inserting feedback
        $sql = "
            INSERT INTO feedback (reservationid, message)
            VALUES (:reservation_id, :message)
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":reservation_id", $this->reservation_id);
        $stmt->bindParam(":message", $this->message);
        return $stmt->execute();
    }
}
?>