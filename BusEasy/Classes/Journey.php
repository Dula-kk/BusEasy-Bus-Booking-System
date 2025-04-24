<?php

class Journey {
    private $conn;
    private $journey_id;
    private $conductor_id;
    private $route;
    private $departure_time;
    private $arrival_time;
    private $status;
    private $date;
    private $fee;

    public function __construct($journey_id = null, $conductor_id = null, $route = null, $departure_time = null) {
        $this->journey_id = $journey_id;
        $this->conductor_id = $conductor_id;
        $this->route = $route;
        $this->departure_time = $departure_time;

        // Establish database connection
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getArrivalTime() {
        return $this->arrival_time;
    }
    
    public function setArrivalTime($arrival_time) {
        $this->arrival_time = $arrival_time;
    }
    
    public function getDate() {
        return $this->date;
    }
    
    public function setDate($date) {
        $this->date = $date;
    }
    
    public function getFee() {
        return $this->fee;
    }
    
    public function setFee($fee) {
        $this->fee = $fee;
    }
    public function getJourneyId() {
        return $this->journey_id;
    }

    public function setJourneyId($journey_id) {
        $this->journey_id = $journey_id;
    }

    public function getConductorId() {
        return $this->conductor_id;
    }

    public function setConductorId($conductor_id) {
        $this->conductor_id = $conductor_id;
    }

    public function getRoute() {
        return $this->route;
    }

    public function setRoute($route) {
        $this->route = $route;
    }

    public function getDepartureTime() {
        return $this->departure_time;
    }

    public function setDepartureTime($departure_time) {
        $this->departure_time = $departure_time;
    }

    public function getJourneys($conductor_id) {
        $sql = "SELECT * FROM journey WHERE conductorid = :conductor_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':conductor_id', $conductor_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function getReservationsForJourney($journey_id) {
        $sql = "SELECT * FROM reservation WHERE journeyid = :journey_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':journey_id', $journey_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addJourney() {
        try {
            $sql = "INSERT INTO journey (conductorid, route, departure_time, arrival_time, date, fee, status) 
                    VALUES (:conductor_id, :route, :departure_time, :arrival_time, :date, :fee, 'pending')";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':conductor_id', $this->conductor_id, PDO::PARAM_INT);
            $stmt->bindParam(':route', $this->route, PDO::PARAM_STR);
            $stmt->bindParam(':departure_time', $this->departure_time, PDO::PARAM_STR);
            $stmt->bindParam(':arrival_time', $this->arrival_time, PDO::PARAM_STR);
            $stmt->bindParam(':date', $this->date, PDO::PARAM_STR);
            $stmt->bindParam(':fee', $this->fee, PDO::PARAM_STR);
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            return "Error adding journey: " . $e->getMessage();
        }
    }


    public function getJourneysByStatus($conductor_id, $status) {
        try {
            $sql = "SELECT * FROM journey 
                    WHERE conductorid = :conductor_id 
                    AND status = :status
                    ORDER BY departure_time ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':conductor_id', $conductor_id, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error retrieving journeys: " . $e->getMessage();
        }
    }

    // Update journey details
    public function editJourneyDetails($journey_id, $route, $departure_time, $arrival_time, $date, $fee) {
        try {
            $sql = "UPDATE journey 
                    SET route = :route,
                        departure_time = :departure_time,
                        arrival_time = :arrival_time, 
                        date = :date,
                        fee = :fee
                    WHERE journeyid = :journey_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':journey_id', $journey_id, PDO::PARAM_INT);
            $stmt->bindParam(':route', $route, PDO::PARAM_STR);
            $stmt->bindParam(':departure_time', $departure_time, PDO::PARAM_STR);
            $stmt->bindParam(':arrival_time', $arrival_time, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':fee', $fee, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            return "Error updating journey: " . $e->getMessage();
        }
    }

    // Delete a journey and associated reservations
    public function deleteJourney($journey_id) {
        try {
            // First delete associated reservations
            $sql = "DELETE FROM reservation WHERE journeyid = :journey_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':journey_id', $journey_id, PDO::PARAM_INT);
            $stmt->execute();

            // Then delete the journey
            $sql = "DELETE FROM journey WHERE journeyid = :journey_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':journey_id', $journey_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return "Error deleting journey: " . $e->getMessage();
        }
    }

    // Get all scheduled journeys
    public static function getScheduledJourneys($conn) {
        try {
            $sql = "SELECT * FROM journey 
                    WHERE status = 'scheduled'
                    ORDER BY departure_time ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error retrieving scheduled journeys: " . $e->getMessage();
        }
    }

    public static function getReservedSeats($conn, $journeyId) {
        $query = "SELECT seats FROM reservation WHERE journeyid = :journey_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':journey_id', $journeyId, PDO::PARAM_INT);
        $stmt->execute();
    
        
        $reservedSeats = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $seats = explode(',', $row['seats']); // Split by commas
            $reservedSeats = array_merge($reservedSeats, array_map('intval', $seats)); // Convert to integers
        }
        return $reservedSeats;
    }
    
    public static function getJourneyDetailsById($conn, $journeyId) {
        try {
            $sql = "SELECT * FROM journey WHERE journeyid = :journey_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':journey_id', $journeyId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error retrieving journey details: " . $e->getMessage();
        }
    }

    public static function getUpcomingJourneys($conn, $user_id) {
        $sql = "SELECT * FROM journey WHERE user_id = ? AND departure_date > NOW() AND status = 'Upcoming'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function getPreviousJourneys($conn, $user_id) {
        $sql = "SELECT * FROM journey WHERE user_id = ? AND departure_date <= NOW() AND status = 'Completed'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

?>
