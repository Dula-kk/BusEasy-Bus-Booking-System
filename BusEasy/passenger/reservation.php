<?php
require_once('../Classes/Database.php');
require_once('../Classes/Journey.php');
require_once('../Classes/Reservation.php');
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$database = new Database();
$conn = $database->connect();

$scheduledJourneys = Journey::getScheduledJourneys($conn);

if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $filteredJourneys = array_filter($scheduledJourneys, function($journey) use ($searchQuery) {
        return stripos($journey['route'], $searchQuery) !== false;
    });

    foreach ($filteredJourneys as &$journey) {
        $reservedSeats = Journey::getReservedSeats($conn, $journey['journeyid']);
        $journey['reserved_seats'] = !empty($reservedSeats) ? array_map('intval', $reservedSeats) : [];
    }
    unset($journey);

    header('Content-Type: application/json');
    echo json_encode(array_values($filteredJourneys)); 
    exit;
}

foreach ($scheduledJourneys as &$journey) {
    $reservedSeats = Journey::getReservedSeats($conn, $journey['journeyid']);
    $journey['reserved_seats'] = !empty($reservedSeats) ? array_map('intval', $reservedSeats) : [];
}
unset($journey);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_reservation') {
    $reservation = new Reservation($user_id);
    $reservation_id = $reservation->createReservation($_POST['journeyid'], $_POST['seats']);
    if (is_numeric($reservation_id)) {
        echo "<script>alert('Reservation created successfully!');</script>";
        echo "<script>window.location.href = 'payment.php?reservation_id=" . $reservation_id . "&total=" . $_POST['total'] . "';</script>";
    } else {
        echo "<script>alert('Failed to create reservation');</script>";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve Seat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/style2.css">
    <style>
        .blur {
            filter: blur(3px);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
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
        <h4 class="text-center">Welcome Back <?php echo $_SESSION['username']; ?>!</h4>
        <h2 class="text-center mb-4">Reserve Your Seat</h2>
        <div class="input-group mb-4">
            <input type="text" class="form-control" id="searchBar" placeholder="Search bus route">
            <button class="btn btn-dark" onclick="searchBus()">Search</button>
        </div>

        <div id="busList">
            <h4 class="mb-3">Available Buses</h4>
            <?php
            if (is_array($scheduledJourneys)) {
                echo '<ul class="list-group">';
                foreach ($scheduledJourneys as $journey) {
                    echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                    echo $journey['journeyid'] . ' - ' . $journey['route'] . ' - Departure: ' . $journey['departure_time'] . ', Arrival: ' . $journey['arrival_time'];
                    echo '<button class="btn btn-primary btn-sm" data-journey-id="' . $journey['journeyid'] . '" onclick="selectBus(this, \'' . $journey['route'] . '\', \'' . 
                            $journey['departure_time'] . '\', \'' . $journey['arrival_time'] . '\', ' . $journey['journeyid'] . ', ' . 
                            $journey['fee'] . ')">Select</button>'; 
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p class="text-center">No scheduled journeys available</p>';
            }
            ?>
        </div>
    </div>

    <div class="modal" id="seatModal" tabindex="-1" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="btn-close" onclick="closeModal()"></button>
                <h3 class="text-center mb-3">Select Your Seats</h3>
                <p><strong>Route:</strong> <span id="selectedRoute"></span><br>
                <strong>Departure:</strong> <span id="departureTime"></span><br>
                <strong>Arrival:</strong> <span id="arrivalTime"></span></p>
                <div id="seatContainer" class="seat-reservation">
                </div>
                <div class="mt-3">
                    <p><strong>Booking Charge:</strong> Rs. 100 <br>
                    <strong>Seat Price:</strong> <span id="seatPrice"></span> <br>
                    <strong>Total Price:</strong> <span id="totalPrice">Rs. 0</span></p>
                </div>
                <input type="hidden" id="journeyId" name="journeyId">
                <button class="btn btn-primary w-100" onclick="makeReservation()">Reserve Now</button>
            </div>
        </div>
    </div>
    <div class="container mt-4 w-auto" id="voice" style="border: 1px solid #ffffff38; padding: 10px; border-radius: 12px; background: #f9f9f9; display: flex; align-items: center; gap: 20px; padding: 10px 20px; margin-bottom: 100px;">
        <img id="start-recording" src="../img/mic.png" alt="Start Recording">
        <img id="stop-recording" src="../img/STOP.png" alt="Stop Recording">
        <p id="user-command" class="mt-3">
            Voice-Activated Seat Reservation
        </p>
    </div>


    <script>
    // Declare variables 
    let selectedSeats = [];
    let totalPrice = 0;
    const bookingCharge = 100;
    const journeys = <?php echo json_encode($scheduledJourneys); ?>;
    let recognition;
    try {
        recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    } catch (error) {
        alert('Speech Recognition is not supported in this browser.');
    }

    const startBtn = document.getElementById('start-recording');
    const stopBtn = document.getElementById('stop-recording');
    const commandDisplay = document.getElementById('user-command');
    const bookingConfirmation = document.getElementById('booking-confirmation');
    const bookingMessage = document.getElementById('booking-message');
    const proceedToPaymentBtn = document.getElementById('proceed-to-payment');

    let isListening = false;

    if (startBtn && stopBtn) {
        startBtn.addEventListener('click', () => {
            if (!recognition) {
                alert('Speech Recognition is not supported in this browser.');
                return;
            }

            startBtn.style.display = 'none';
            stopBtn.style.display = 'block';
            commandDisplay.textContent = 'Listening...';

            recognition.lang = 'en-US';
            recognition.interimResults = true; 
            recognition.start();
            isListening = true;

            recognition.onresult = (event) => {
                let interimTranscript = '';
                let finalTranscript = '';

                for (let i = event.resultIndex; i < event.results.length; i++) {
                    const transcript = event.results[i][0].transcript;
                    if (event.results[i].isFinal) {
                        finalTranscript += transcript;
                    } else {
                        interimTranscript += transcript;
                    }
                }

                commandDisplay.innerHTML = `"${finalTranscript || interimTranscript}"`;

                if (finalTranscript) {
                    const userCommand = finalTranscript.toLowerCase();

                    if (userCommand.includes('book')) {
                        const match = userCommand.match(/book (\d+) tickets? (?:of|for|on)? bus(?: number)? (\d+)/i);  //(\d+) → Captures one or more digits,/i → Case-insensitive matching.
                        if (match) {
                            const numberOfTickets = match[1];
                            const journeyId = match[2];
                            alert('You have requested to book ' + numberOfTickets + ' tickets for the ' + journeyId + ' journey.');
                        } else {
                            alert('Please specify the number of tickets and the journey id. ' + numberOfTickets + ' ' + journeyId   );
                        }
                    } else if (userCommand.includes('search')) {
                        const query = userCommand.replace('search', '').trim();
                        document.getElementById('searchBar').value = query;
                        searchBus();
                    } else if (userCommand.includes('select')) {
                        const match = userCommand.match(/select (?:bus|journey)? (\d+)/i);
                        if (match) {
                            const journeyId = match[1]; 
                            selectBusByVoice(journeyId);
                        } else {
                            alert('Invalid command. Please say something like "select (journey id)".');
                        }
                    } else {
                        alert('Invalid command');
                    }
                }
            };

            recognition.onerror = (event) => {
                console.error('Speech recognition error:', event.error);
                resetVoiceControls();
            };
        });

        stopBtn.addEventListener('click', () => {
            if (recognition && isListening) {
                recognition.stop();
                isListening = false;
                startBtn.style.display = 'block';
                stopBtn.style.display = 'none';
                commandDisplay.textContent = 'Stopped listening.';
            }
        });
    } else {
        console.error('Start or Stop button not found in the DOM.');
    }


    function resetVoiceControls() {
        startBtn.style.display = 'block';
        stopBtn.style.display = 'none';
        commandDisplay.textContent = '';
        bookingConfirmation.style.display = 'none';
    }
    function selectBusByVoice(routeOrId) {
        const busList = document.querySelectorAll('#busList .list-group-item');
        let found = false;

        busList.forEach(bus => {
            const routeText = bus.textContent.toLowerCase();
            const button = bus.querySelector('button');

            if (routeText.includes(routeOrId.toLowerCase()) || button.getAttribute('data-route-id') === routeOrId) {
                if (button) {
                    button.click();
                    found = true;
                }
            }
        });

        if (!found) {
            alert(`No bus found for route or ID: ${routeOrId}`);
        }
    }
    function searchBus() {
        const searchQuery = document.getElementById('searchBar').value.trim();

        fetch(`reservation.php?search=${encodeURIComponent(searchQuery)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const busList = document.getElementById('busList');
                busList.innerHTML = '<h4 class="mb-3">Available Buses</h4>';

                if (data.length > 0) {
                    const ul = document.createElement('ul');
                    ul.classList.add('list-group');

                    data.forEach(journey => {
                        const li = document.createElement('li');
                        li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                        li.innerHTML = `
                            ${journey.journeyid} - ${journey.route} - Departure: ${journey.departure_time}, Arrival: ${journey.arrival_time}
                            <button class="btn btn-primary btn-sm" onclick="selectBus(this, '${journey.route}', '${journey.departure_time}', '${journey.arrival_time}', ${journey.journeyid}, ${journey.fee})">Select</button>
                        `;
                        ul.appendChild(li);
                    });

                    busList.appendChild(ul);
                } else {
                    busList.innerHTML += '<p class="text-center">No scheduled journeys available</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                alert('Failed to fetch data. Please try again.');
            });
    }

    stopBtn.addEventListener('click', () => {
        if (isListening && recognition) {
            recognition.stop();
            resetVoiceControls();
        }
    });

    proceedToPaymentBtn.addEventListener('click', () => {
        // Redirect to the payment page
        window.location.href = '/payment'; 
    });

    function resetVoiceControls() {
        startBtn.style.display = 'block';
        stopBtn.style.display = 'none';
        commandDisplay.textContent = 'Your command will appear here...';
        isListening = false;
    }
    function resetVoiceControls() {
        startBtn.style.display = 'block';
        stopBtn.style.display = 'none';
        commandDisplay.textContent = 'Voice recognition stopped.';
        bookingConfirmation.style.display = 'none';
    }
    

    document.getElementById('searchBar').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            searchBus();
        }
    });

    function selectBus(button, route, departure, arrival, journeyId, fare) {
        console.log("selectBus called"); 
        console.log("selectedSeats:", selectedSeats); 
        console.log("totalPrice:", totalPrice); 

        // Reset selectedSeats and calculate totalPrice
        selectedSeats = []; 
        totalPrice = bookingCharge + fare;
        updateTotalPrice();

        // Update modal content
        document.getElementById('selectedRoute').textContent = route;
        document.getElementById('departureTime').textContent = departure;
        document.getElementById('arrivalTime').textContent = arrival;
        document.getElementById('seatPrice').textContent = fare;
        document.getElementById('journeyId').value = journeyId; 

        // Generate seats
        const seatContainer = document.getElementById('seatContainer');
        seatContainer.innerHTML = ''; 

        const journey = journeys.find(j => j.journeyid == journeyId);
        const reservedSeats = journey && journey.reserved_seats ? journey.reserved_seats : [];

        for (let i = 1; i <= 20; i++) {
            const seat = document.createElement('div');
            seat.classList.add('seat');
            seat.textContent = i;

            if (reservedSeats.includes(i)) {
                seat.classList.add('reserved');
                seat.style.backgroundColor = '#ccc';
                seat.style.cursor = 'not-allowed';
                seat.style.pointerEvents = 'none';
            } else {
                seat.addEventListener('click', () => toggleSeat(seat, i, fare)); 
            }

            seatContainer.appendChild(seat);
        }

        // Highlight the selected bus
        const busList = document.querySelectorAll('#busList .list-group-item');
        busList.forEach(bus => bus.classList.remove('bus-active'));
        button.closest('.list-group-item').classList.add('bus-active');

        // Show the modal
        const modal = document.getElementById('seatModal');
        modal.style.display = 'block';
        document.querySelector('.main').classList.add('blur');
    }

    function toggleSeat(seat, seatNumber, fare) {
        if (seat.classList.contains('selected')) {
            seat.classList.remove('selected');
            selectedSeats = selectedSeats.filter(s => s !== seatNumber);
        } else {
            seat.classList.add('selected');
            selectedSeats.push(seatNumber);
        }

        // Update total price
        totalPrice = bookingCharge + selectedSeats.length * fare;
        updateTotalPrice();
    }

    function updateTotalPrice() {
        document.getElementById('totalPrice').textContent = `Rs. ${totalPrice}`;
    }

    function closeModal() {
        const modal = document.getElementById('seatModal');
        modal.style.display = 'none';
        document.querySelector('.main').classList.remove('blur');
    }

    function makeReservation() {
        const journeyId = document.getElementById('journeyId').value;
        if (!selectedSeats.length || !journeyId) {
            alert("Please select at least one seat.");
            return;
        }

        const confirmation = confirm(`Seats Reserved: ${selectedSeats.join(', ')}\nTotal Price: Rs. ${totalPrice}`);
        if (confirmation) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = ''; 
            
            form.innerHTML = `
                <input type="hidden" name="action" value="create_reservation">
                <input type="hidden" name="journeyid" value="${journeyId}">
                <input type="hidden" name="seats" value="${selectedSeats.join(',')}">
                <input type="hidden" name="total" value="${totalPrice}">
            `;
            document.body.appendChild(form);
            form.submit();
        }                                       //Book Tickets: "Book 3 tickets for bus ---."
                                                //Search: "Search ---."
                                                //Select Bus: "Select bus ---."
    }

    window.addEventListener('click', (event) => {
        const modal = document.getElementById('seatModal');
        if (event.target === modal) {
            closeModal();
        }
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>