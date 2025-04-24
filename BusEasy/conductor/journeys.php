<?php
session_start();
require_once('../Classes/Database.php');
require_once('../Classes/Journey.php');

// htmlspecialchars() ensures that special characters (like <, >, &, ") are converted to HTML entities to prevent XSS (Cross-Site Scripting) attacks.
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$conductor_id = $_SESSION['user_id'];
$journey = new Journey($conductor_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_journey') {
    $journey = new Journey();
    
    $journey->setConductorId($conductor_id);
    $journey->setRoute($_POST['route']);
    $journey->setDepartureTime($_POST['departure_time']);
    $journey->setArrivalTime($_POST['arrival_time']);
    $journey->setDate($_POST['date']);
    $journey->setFee($_POST['fee']);
    
    
    $result = $journey->addJourney();
    
    if (is_numeric($result)) {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=Journey added successfully');
        exit;
    } else {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?error=' . urlencode($result));
        exit;
    }
}
// Get scheduled and completed journeys
$scheduled_journeys = $journey->getJourneysByStatus($conductor_id, 'scheduled');
$completed_journeys = $journey->getJourneysByStatus($conductor_id, 'completed');


// Handle journey deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_journey') {
    if (isset($_POST['journey_id'])) {
        $result = $journey->deleteJourney($_POST['journey_id']);
        if ($result === true) {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?success=Journey deleted successfully');
            exit;
        } else {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?error=' . urlencode($result));
            exit;
        }
    }
}

// Handle journey editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_journey') {
    if (isset($_POST['journey_id'])) {
        $result = $journey->editJourneyDetails(
            $_POST['journey_id'],
            $_POST['route'],
            $_POST['departure_time'],
            $_POST['arrival_time'], 
            $_POST['date'],
            $_POST['fee']
        );
        if ($result === true) {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?success=Journey updated successfully');
            exit;
        } else {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?error=' . urlencode($result));
            exit;
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reschedule_journey') {
    $journey = new Journey();
    // Set journey details from form data
    $journey->setConductorId($conductor_id);
    $journey->setRoute($_POST['reschedule_route']);
    $journey->setDepartureTime($_POST['reschedule_departure_time']);
    $journey->setArrivalTime($_POST['reschedule_arrival_time']);
    $journey->setDate($_POST['reschedule_date']);
    $journey->setFee($_POST['reschedule_fee']);
    
    // Add journey and check for success or error
    $result = $journey->addJourney();
    
    if (is_numeric($result)) {
        // Redirect with success message
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=Journey rescheduled successfully');
        exit;
    } else {
        // Redirect with error message
        header('Location: ' . $_SERVER['PHP_SELF'] . '?error=' . urlencode($result));
        exit;
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journey Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/style3.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark ">
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
                        <a class="nav-link active" href="reservations.php">View reservation</a>
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

    <div class="main">
        <div class="container mt-3">
            <h2 class="text-center">My Journeys</h2>
            <?php
            // Display success message if present
            if (isset($_GET['success'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        ' . htmlspecialchars($_GET['success']) . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
            }

            // Display error message if present  
            if (isset($_GET['error'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ' . htmlspecialchars($_GET['error']) . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
            }
            ?>
            <div class="text-center">
                <button class="btn btn-dark mb-3" data-bs-toggle="modal" data-bs-target="#addJourneyModal">Add Journey</button>
            </div>
            <h5>Upcoming Journeys</h5>
            
            <?php if (!empty($scheduled_journeys)): ?>
                <div class="row">
                    <?php foreach ($scheduled_journeys as $journey): ?>
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Journey ID: <?php echo htmlspecialchars($journey['journeyid']); ?></h5>
                                    <p class="card-text"><strong>Route:</strong> <?php echo htmlspecialchars($journey['route']); ?></p>
                                    <p class="card-text"><strong>Departure:</strong> <?php echo htmlspecialchars($journey['departure_time']); ?></p>
                                    <p class="card-text"><strong>Arrival:</strong> <?php echo htmlspecialchars($journey['arrival_time']); ?></p>
                                    <p class="card-text"><strong>Date:</strong> <?php echo htmlspecialchars($journey['date']); ?></p>
                                    <p class="card-text"><strong>Fare:</strong> <?php echo htmlspecialchars($journey['fee']); ?></p>
                                    <div class="text-center">
                                        <button class="btn btn-dark btn-sm me-2" onclick="deleteJourney(<?php echo htmlspecialchars($journey['journeyid']); ?>)">
                                            Delete
                                        </button>
                                        <button class="btn btn-dark btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-journey-id="<?php echo htmlspecialchars($journey['journeyid']); ?>"
                                                data-route="<?php echo htmlspecialchars($journey['route']); ?>"
                                                data-departure="<?php echo htmlspecialchars($journey['departure_time']); ?>"
                                                data-arrival="<?php echo htmlspecialchars($journey['arrival_time']); ?>"
                                                data-date="<?php echo htmlspecialchars($journey['date']); ?>"
                                                data-fare="<?php echo htmlspecialchars($journey['fee']); ?>">
                                            Edit
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center">No upcoming journeys found.</p>
            <?php endif; ?>
            
            <h5>Previous Journeys</h5>
            <?php if (!empty($completed_journeys)): ?>
                <div class="row">
                    <?php foreach ($completed_journeys as $journey): ?>
                        <div class="col-md-4">                             
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Journey ID: <?php echo htmlspecialchars($journey['journeyid']); ?></h5>
                                    <p class="card-text"><strong>Route:</strong> <?php echo htmlspecialchars($journey['route']); ?></p>
                                    <p class="card-text"><strong>Departure:</strong> <?php echo htmlspecialchars($journey['departure_time']); ?></p>
                                    <p class="card-text"><strong>Arrival:</strong> <?php echo htmlspecialchars($journey['arrival_time']); ?></p>
                                    <p class="card-text"><strong>Date:</strong> <?php echo htmlspecialchars($journey['date']); ?></p>
                                    <p class="card-text"><strong>Fare:</strong> <?php echo htmlspecialchars($journey['fee']); ?></p>
                                    <div class="text-center">
                                        <button class="btn btn-dark btn-sm me-2" data-bs-toggle="modal" data-bs-target="#rescheduleModal"
                                                data-route="<?php echo htmlspecialchars($journey['route']); ?>"
                                                data-departure="<?php echo htmlspecialchars($journey['departure_time']); ?>"
                                                data-arrival="<?php echo htmlspecialchars($journey['arrival_time']); ?>"
                                                data-date="<?php echo htmlspecialchars($journey['date']); ?>"
                                                data-fare="<?php echo htmlspecialchars($journey['fee']); ?>">
                                            Reschedule
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center">No upcoming journeys found.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal fade" id="addJourneyModal" tabindex="-1" aria-labelledby="addJourneyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addJourneyModalLabel">Add Journey</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add_journey">
                        <div class="mb-3">
                            <label for="route" class="form-label">Route</label>
                            <input type="text" class="form-control" name="route" id="route" required>
                        </div>
                        <div class="mb-3">
                            <label for="departure_time" class="form-label">Departure Time</label>
                            <input type="time" class="form-control" name="departure_time" id="departure_time" required>
                        </div>
                        <div class="mb-3">
                            <label for="arrival_time" class="form-label">Arrival Time</label>
                            <input type="time" class="form-control" name="arrival_time" id="arrival_time" required>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" name="date" id="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="fee" class="form-label">Fee</label>
                            <input type="number" class="form-control" name="fee" id="fee" step="0.01" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Journey</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editJourneyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editJourneyModalLabel">Edit Journey</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="edit_journey">
                        <input type="hidden" name="journey_id" id="edit_journey_id">
                        <div class="mb-3">
                            <label for="edit_route" class="form-label">Route</label>
                            <input type="text" class="form-control" name="route" id="edit_route" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_departure_time" class="form-label">Departure Time</label>
                            <input type="time" class="form-control" name="departure_time" id="edit_departure_time" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_arrival_time" class="form-label">Arrival Time</label>
                            <input type="time" class="form-control" name="arrival_time" id="edit_arrival_time" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_date" class="form-label">Date</label>
                            <input type="date" class="form-control" name="date" id="edit_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_fee" class="form-label">Fee</label>
                            <input type="number" class="form-control" name="fee" id="edit_fee" step="0.01" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Journey</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="rescheduleModalLabel">Reschedule Journey</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="reschedule_journey">
                        <div class="mb-3">
                            <input type="hidden" class="form-control" name="reschedule_route" id="reschedule_route" required>
                        </div>
                        <div class="mb-3">
                            <label for="departure_time" class="form-label">Departure Time</label>
                            <input type="time" class="form-control" name="reschedule_departure_time" id="reschedule_departure_time" required>
                        </div>
                        <div class="mb-3">
                            <label for="arrival_time" class="form-label">Arrival Time</label>
                            <input type="time" class="form-control" name="reschedule_arrival_time" id="reschedule_arrival_time" required>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" name="reschedule_date" id="reschedule_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="fee" class="form-label">Fee</label>
                            <input type="number" class="form-control" name="reschedule_fee" id="reschedule_fee" step="0.01" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Journey</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        
        // Edit Modal Handler
        var editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var journeyId = button.getAttribute('data-journey-id');
            var route = button.getAttribute('data-route');
            var departure = button.getAttribute('data-departure');
            var arrival = button.getAttribute('data-arrival');
            var date = button.getAttribute('data-date');
            var fare = button.getAttribute('data-fare');

            // Populate the modal fields
            document.getElementById('edit_journey_id').value = journeyId;
            document.getElementById('edit_route').value = route;
            document.getElementById('edit_departure_time').value = departure;
            document.getElementById('edit_arrival_time').value = arrival;
            document.getElementById('edit_date').value = date;
            document.getElementById('edit_fee').value = fare;
        });
        
        var rescheduleModal = document.getElementById('rescheduleModal');
        rescheduleModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var route = button.getAttribute('data-route');
            var departure = button.getAttribute('data-departure');
            var arrival = button.getAttribute('data-arrival');
            var date = button.getAttribute('data-date');
            var fare = button.getAttribute('data-fare');

            document.getElementById('reschedule_route').value = route;
            document.getElementById('reschedule_departure_time').value = departure;
            document.getElementById('reschedule_arrival_time').value = arrival;
            document.getElementById('reschedule_date').value = date;
            document.getElementById('reschedule_fee').value = fare;
        });
        
        
        function deleteJourney(journeyId) {
            if (confirm('Are you sure you want to delete this journey?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_journey">
                    <input type="hidden" name="journey_id" value="${journeyId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
