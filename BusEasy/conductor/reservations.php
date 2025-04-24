<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'conductor') {
    header("Location: ../login.php");
    exit();
}
 //Document Object Model
include '../Classes/Database.php';
include '../Classes/Reservation.php';
include '../Classes/Payment.php';

$user_id = $_SESSION['user_id'];
$reservation_obj = new Reservation($user_id);
$error_message = '';

if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['reservation_id'])) {
    $result = $reservation_obj->deleteReservation($_POST['reservation_id']);
    if ($result === true) {
        header("Location: reservations.php?journey_id=" . $_GET['journey_id']);
        exit();
    } else {
        $error_message = $result;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'start_journey') {
    if (isset($_POST['journey_id'])) {
        $journeyId = $_POST['journey_id'];

        $result = $reservation_obj->setJourneyStarted($journeyId);
        if ($result === true) {
            // Redirect back with a success message
            header("Location: reservations.php?success=Journey started successfully");
            exit();
        } else {
            // Handle error, e.g., show a message on the page
            $error_message = $result;
        }
    } else {
        $error_message = 'Invalid journey ID.';
    }
}


$upcomingRoutes = $reservation_obj->getUpcomingRoutes();
if (!is_array($upcomingRoutes)) {
    die("Error retrieving upcoming routes: " . $upcomingRoutes);
}

$selectedJourneyId = isset($_GET['journey_id']) ? $_GET['journey_id'] : null;
$reservations = [];
$journeySummary = null;

if ($selectedJourneyId) {
    $reservations = $reservation_obj->getReservations($selectedJourneyId);
    if (!is_array($reservations)) {
        die("Error retrieving reservations: " . $reservations);
    }
    
    $journeySummary = $reservation_obj->getJourneySummary($selectedJourneyId);
    if (!is_array($journeySummary)) {
        die("Error retrieving journey summary: " . $journeySummary);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conductor's Reservation View</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/style3.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <img src="../img/logo.png" alt="Logo" class="logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fa fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="reservations.php">View Reservation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="journeys.php">My Journeys</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Log out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="bg"></div>

    <div class="main mt-2">
        <div class="container mt-3">
            <div class="centralized-box">
            <h3 class="text-center mb-4">Welcome Back <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
                <h2 class="text-center">View Reservation</h2>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <div class="mb-4">
                    <h4 class="text-center mb-3">Select Route</h4>
                    <select class="form-select" id="routeSelect" onchange="location.href='reservations.php?journey_id=' + this.value;">
                        <option value="">Select a route</option>
                        <?php foreach ($upcomingRoutes as $route): ?>
                            <option value="<?php echo htmlspecialchars($route['journeyid']); ?>"
                                    <?php echo ($route['journeyid'] == $selectedJourneyId) ? 'selected' : ''; ?>
                                    data-route="<?php echo htmlspecialchars($route['route']); ?>"
                                    data-date="<?php echo htmlspecialchars($route['date']); ?>"
                                    data-departure="<?php echo htmlspecialchars($route['departure_time']); ?>">
                                Route: <?php echo htmlspecialchars($route['route']); ?> - 
                                Departure: <?php echo htmlspecialchars($route['date'] . " " . $route['departure_time']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($selectedJourneyId): ?>
                    <div class="route-details mb-4">
                        <h4 class="text-center">Route Details</h4>
                        <p id="routeDetail"><strong>Route:</strong></p>
                        <p id="dateDetail"><strong>Date:</strong></p>
                        <p id="departureDetail"><strong>Departure Time:</strong></p>
                    </div>

                    <div class="row">
                        <?php foreach ($reservations as $reservation): ?>
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <p class="card-title">
                                            <strong>Passenger Name:</strong> 
                                            <?php echo htmlspecialchars($reservation_obj->getUsernameById($reservation['userid'])); ?>
                                        </p>
                                        <p class="card-text"><strong>Seats:</strong> <?php echo htmlspecialchars($reservation['seats']); ?></p>
                                        <p class="card-text"><strong>Payment Status:</strong> <?php echo htmlspecialchars($reservation['payment_status']); ?></p>
                                        <button class="btn btn-dark btn-sm" onclick="deleteReservation(<?php echo $reservation['reservationid']; ?>)">Delete</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div id="summary" class="mt-4">
                        <h4 class="text-center">Journey Summary</h4>
                        <div class="row justify-content-center">
                            <div class="col-md-4">
                                <div class="card text-center mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Reservations</h5>
                                        <p class="card-text"><?php echo $journeySummary['total']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Paid online</h5>
                                        <p class="card-text"><?php echo $journeySummary['paid']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Pay on Arrival</h5>
                                        <p class="card-text"><?php echo $journeySummary['pending']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="text-center">
                    <button class="btn btn-dark my-4" onclick="startJourney(<?php echo $selectedJourneyId; ?>)">Journey Started</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showRouteDetails() {
            const select = document.getElementById('routeSelect');
            const selectedOption = select.options[select.selectedIndex];

            if (select.value) {
                const route = selectedOption.dataset.route;
                const date = selectedOption.dataset.date;
                const departureTime = selectedOption.dataset.departure;

                document.getElementById('routeDetail').innerHTML = `<strong>Route:</strong> ${route}`;
                document.getElementById('dateDetail').innerHTML = `<strong>Date:</strong> ${date}`;
                document.getElementById('departureDetail').innerHTML = `<strong>Departure Time:</strong> ${departureTime}`;
            }
        }

        function deleteReservation(reservationId) {
            if (confirm('Are you sure you want to delete this reservation?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="reservation_id" value="${reservationId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function startJourney(journeyId) {
            if (confirm('Are you sure you want to mark this journey as started?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="start_journey">
                    <input type="hidden" name="journey_id" value="${journeyId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        document.addEventListener('DOMContentLoaded', showRouteDetails);
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
   
</body>
</html