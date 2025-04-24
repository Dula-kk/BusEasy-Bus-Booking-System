<?php
class Reservation {
    private $conn;  // Database connection
    private $user_id;
    private $reserverid;
    private $userid;
    private $journeyid;
    private $seats;
    private $paymentstatus;


    public function __construct($user_id) {
        $database = new Database();
        $this->conn = $database->connect();
        $this->user_id = $user_id;
    }

    // Getters
    public function getReserverId() {
        return $this->reserverid;
    }

    public function getUserId() {
        return $this->userid; 
    }

    public function getJourneyId() {
        return $this->journeyid;
    }

    public function getSeats() {
        return $this->seats;
    }

    public function getPaymentStatus() {
        return $this->paymentstatus;
    }

    // Setters 
    public function setReserverId($reserverid) {
        $this->reserverid = $reserverid;
    }

    public function setUserId($userid) {
        $this->userid = $userid;
    }

    public function setJourneyId($journeyid) {
        $this->journeyid = $journeyid;
    }

    public function setSeats($seats) {
        $this->seats = $seats;
    }

    public function setPaymentStatus($paymentstatus) {
        $this->paymentstatus = $paymentstatus;
    }
    public function getUpcomingRoutes() {
        try {
            $query = "SELECT * FROM journey 
                      WHERE conductorId = :user_id 
                      AND status = 'scheduled'
                      ORDER BY departure_time ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error retrieving routes: " . $e->getMessage();
        }
    }

    public function getReservations($journeyId) {
        try {
            $query = "SELECT * FROM reservation 
                      WHERE journeyid = :journey_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":journey_id", $journeyId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error retrieving reservations: " . $e->getMessage();
        }
    }

    public function getUsernameById($userId) {
        try {
            $query = "SELECT firstname, lastname 
                      FROM user 
                      WHERE userid = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['firstname'] . ' ' . $result['lastname'] : null;
        } catch (PDOException $e) {
            return "Error retrieving user: " . $e->getMessage();
        }
    }

    public function getJourneySummary($journeyId) {
        try {
            $query = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN payment_status = 'completed' THEN 1 ELSE 0 END) as paid,
                        SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending
                      FROM reservation 
                      WHERE journeyid = :journey_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":journey_id", $journeyId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error retrieving journey summary: " . $e->getMessage();
        }
    }

    public function deleteReservation($reservationId) {
        try {
            $query = "DELETE FROM reservation 
                      WHERE reservationid = :reservation_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":reservation_id", $reservationId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return "Error deleting reservation: " . $e->getMessage();
        }
    }

    public function getAllJourneys() {
        try {
            $query = "SELECT * FROM journey 
                      WHERE conductorId = :user_id 
                      ORDER BY departure_time ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error retrieving journeys: " . $e->getMessage();
        }
    }

    public function setJourneyStarted($journeyId) {
        try {
            $query = "UPDATE journey 
                      SET status = 'completed' 
                      WHERE journeyid = :journey_id;";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":journey_id", $journeyId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return "Error starting journey: " . $e->getMessage();
        }
    }

    public function createReservation($journeyId, $seats) {
        try {
            // Insert reservation data
            $query = "INSERT INTO reservation (userid, journeyid, seats, payment_status) 
                        VALUES (:userid, :journeyid, :seats, 'pending')";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":userid", $this->user_id);
            $stmt->bindParam(":journeyid", $journeyId);
            $stmt->bindParam(":seats", $seats);
            $stmt->execute();
            $reservationId = $this->conn->lastInsertId();
            
            return $reservationId; 
        } catch (PDOException $e) {
            return "Error creating reservation: " . $e->getMessage();
        }
    }

    public static function getReservationDetailsById($conn, $reservationId) {
        try {
            $query = "SELECT * FROM reservation WHERE reservationid = :reservation_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':reservation_id', $reservationId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error retrieving reservation details: " . $e->getMessage();
        }
    }

    public function updateStatus($status) {
        $query = "UPDATE reservation SET payment_status = :status WHERE reservationid = :reservation_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':reservation_id', $this->reserverid);
        return $stmt->execute();
    }

    public static function getReservationsByStatus($conn, $user_id, $journey_status) {
        $sql = "
            SELECT r.reservationid, j.route, j.date, j.departure_time, j.status, 
                    r.seats, r.payment_status, p.amount
            FROM reservation r
            JOIN journey j ON r.journeyid = j.journeyid
            JOIN payment p ON r.reservationid = p.reservationid
            WHERE r.userid = :user_id AND j.status = :journey_status
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":journey_status", $journey_status);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getPreviousReservations($conn, $user_id) {
        $sql = "
            SELECT 
                r.reservationid, 
                j.route, 
                j.date, 
                j.departure_time, 
                j.status AS journey_status, 
                r.seats, 
                r.payment_status, 
                p.amount
            FROM reservation r
            JOIN journey j ON r.journeyid = j.journeyid
            LEFT JOIN payment p ON r.reservationid = p.reservationid
            WHERE r.userid = :user_id 
                AND j.status = 'completed'
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getReservationById($conn, $reservation_id) {
        $sql = "
            SELECT u.firstname, u.lastname, r.reservationid, j.route, j.date, j.departure_time, j.status, j.fee, 
                    r.seats, r.payment_status, p.amount
            FROM reservation r
            JOIN journey j ON r.journeyid = j.journeyid
            JOIN payment p ON r.reservationid = p.reservationid
            JOIN user u ON r.userid = u.userid
            WHERE r.reservationid = :reservation_id
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":reservation_id", $reservation_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
}
?>