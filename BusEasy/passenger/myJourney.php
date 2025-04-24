<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once('../Classes/Reservation.php');
require_once('../Classes/Feedback.php');
require_once('../Classes/Refund.php');
require_once('../Classes/Database.php');

$db = new Database();
$conn = $db->connect();
$user_id = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['reservation_id'])) {
    $reservation_id = intval($_GET['reservation_id']); // Sanitize input prevent sql injections,check valid numeric values
    $reservationDetails = Reservation::getReservationById($conn, $reservation_id);
    
    $refund = new Refund();
    $refundStatus = $refund->getRefundStatusByReservationId($reservation_id);
    $reservationDetails['refundStatus'] = $refundStatus;

    if ($reservationDetails) {
        echo json_encode($reservationDetails); // Return reservation details as JSON
    } else {
        echo json_encode(["error" => "Reservation not found"]); // Return error if reservation not found
    }
    exit(); // Stop further execution for AJAX requests
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $reservation_id = intval($_POST['reservation_id']); // Sanitize input
    $feedback_text = trim($_POST['feedback_text']); // Sanitize input

    // Debugging: Log the reservation ID and feedback text
    error_log("Reservation ID: " . $reservation_id);
    error_log("Feedback Text: " . $feedback_text);

    if (!empty($feedback_text)) {
        $feedback = new Feedback();
        $feedback->setReservationId($reservation_id);
        $feedback->setMessage($feedback_text);

        $result = $feedback->createFeedback($conn);
        if ($result === true) {
            echo "<script>alert('Feedback submitted successfully!');</script>";
        } else {
            echo "<script>alert('Failed to submit feedback: " . addslashes($result) . "');</script>"; //Prevents syntax errors when inserting data into SQL queries or handling string literals.
                                                                                                       //Escapes special characters to avoid breaking string formatting.
        }
    } else {
        echo "<script>alert('Please write some feedback before submitting.');</script>";
    }
}

// Handle refund request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_refund') {
    $reservation_id = $_POST['reservation_id'];
    $refund = new Refund();
    $result = $refund->createRefund($reservation_id);

    if ($result === true) {
        echo "<script>alert('Refund requested successfully!');</script>";
    } else {
        echo "<script>alert('Failed to request refund: " . addslashes($result) . "');</script>";
    }
}


$previousReservations = Reservation::getReservationsByStatus($conn, $user_id, "completed");
$upcomingReservations = Reservation::getReservationsByStatus($conn, $user_id, "scheduled");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Journeys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/style2.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <img src="../img/logo.png" alt="Logo" class="logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa fa-bars" aria-hidden="true"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="reservation.php">Reservation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="myJourney.php">My Journeys</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Log out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="bg"></div>
    <div class="main">
        <div class="container mt-5">
            <h2 class="text-center mb-4">My Journeys</h2>

            <!-- Upcoming Reservations -->
            <h3 class="mb-3">Upcoming Reservations</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Route</th>
                        <th>Departure Date</th>
                        <th>Departure Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="upcoming-reservations-table">
                    <?php foreach ($upcomingReservations as $reservation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['reservationid']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['route']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['date']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['departure_time']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['status']); ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="viewDetails(<?php echo $reservation['reservationid']; ?>)">View Details</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Previous Reservations -->
            <h3 class="mb-3 mt-5">Previous Reservations</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Route</th>
                        <th>Departure Date</th>
                        <th>Departure Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="previous-reservations-table">
                    <?php foreach ($previousReservations as $reservation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['reservationid']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['route']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['date']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['departure_time']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['status']); ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="viewDetails(<?php echo $reservation['reservationid']; ?>)">View Details</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="detailsModalLabel">Reservation Details</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="hidden" name="reservation_id" id="reservation-id">
                        <strong>Name:</strong> <span id="customer-name"></span><br>
                    </div>
                    <div class="d-flex">
                        <div class="mb-3 me-5">
                            <strong>No of Seats:</strong> <span id="seats"></span> <br>
                            <strong>Price per Seat:</strong> <span id="fee"></span> <br>
                            <strong>Charge:</strong> <span id="reservation-cost"></span> <br>
                            <strong>Total Amount:</strong> <span id="total-amount"></span> <br>
                        </div>
                        <div class="mb-3">
                            <strong>Route:</strong> <span id="route"></span><br>
                            <strong>Departure Time:</strong> <span id="departure-time"></span> <br>
                            <strong>Departure Date:</strong> <span id="departure-date"></span><br>
                        </div>
                    </div>

                    <div id="feedback-section" style="display: none;">
    <hr>
    <h5 class="mb-3">Feedback</h5>
    <form id="feedback-form" method="POST" action="myJourney.php">
        <input type="hidden" name="reservation_id" id="feedback-reservation-id">
        <div class="mb-3">
            <label for="feedback-text" class="form-label">Your Feedback</label>
            <textarea class="form-control" id="feedback-text" name="feedback_text" rows="3" placeholder="Write your feedback here..."></textarea>
        </div>
        <button type="submit" name="submit_feedback" class="btn btn-dark">Submit Feedback</button>
    </form>
</div>
                <div class="modal-footer">
                    <button id="refund-status" class="btn btn-outline-danger" style="display: none;">Refund  <span id="refund-status-text"></span></button>   
                    <button type="button" class="btn btn-danger" id="refund-button" style="display: none;" onclick="requestRefund()">Request Refund</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
       function viewDetails(reservationId) {
    const detailsModal = new bootstrap.Modal(document.getElementById("detailsModal"));
    detailsModal.show();

    // Set the reservation ID in the hidden input
    document.getElementById("reservation-id").value = reservationId;
    document.getElementById("feedback-reservation-id").value = reservationId;

    fetch(`myJourney.php?reservation_id=${reservationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // Populate modal fields with reservation details
            document.getElementById("customer-name").textContent = data.firstname + " " + data.lastname;
            document.getElementById("route").textContent = data.route;
            document.getElementById("departure-date").textContent = data.date;
            document.getElementById("departure-time").textContent = data.departure_time;
            document.getElementById("seats").textContent = data.seats;
            document.getElementById("fee").textContent = data.fee;
            document.getElementById("reservation-cost").textContent = 100;                                    //data is a JavaScript object containing the JSON response from myJourney.php.
            document.getElementById("total-amount").textContent = data.amount;                                //It is created by response.json() inside the Fetch API.                                                                                                        
                                                                                                             //You can access its values using data.key (e.g., data.date).
                                                                                                             //The error check prevents displaying invalid data if an error occurs.

            // Handle refund button and status display
            const refundButton = document.getElementById("refund-button");
            const refundStatusButton = document.getElementById("refund-status");
            const refundStatusText = document.getElementById("refund-status-text");

            // Hide both buttons initially
            refundButton.style.display = "none";
            refundStatusButton.style.display = "none";

            // Handle different refund statuses
            if (data.status === "scheduled") {
                if (!data.refundStatus || data.refundStatus === "none") {
                    // Show Request Refund button if no refund has been requested
                    refundButton.style.display = "block";
                } else {
                    // Show status button with appropriate text
                    refundStatusButton.style.display = "block";
                    let statusText = "";
                    switch(data.refundStatus) {
                        case "requested":
                            statusText = "Requested";
                            refundStatusButton.className = "btn btn-outline-warning";
                            break;
                        case "approved":
                            statusText = "Approved";
                            refundStatusButton.className = "btn btn-outline-success";
                            break;
                        case "rejected":
                            statusText = "Rejected";
                            refundStatusButton.className = "btn btn-outline-danger";
                            break;
                        default:
                            statusText = data.refundStatus;
                            break;
                    }
                    refundStatusText.textContent = statusText;
                }
            }

            // Show feedback section only for completed reservations
            const feedbackSection = document.getElementById("feedback-section");
            feedbackSection.style.display = (data.status === "completed") ? "block" : "none";
        })
        .catch(error => console.error('Error fetching reservation details:', error));
}


        function requestRefund() {
            const reservationId = document.getElementById("reservation-id").value;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'myJourney.php';


            const reservationIdInput = document.createElement('input');
            reservationIdInput.type = 'hidden';
            reservationIdInput.name = 'reservation_id';
            reservationIdInput.value = reservationId;

            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'request_refund';

            form.appendChild(reservationIdInput);
            form.appendChild(actionInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>