<?php
require_once('../Classes/Reservation.php');
require_once('../Classes/Journey.php');
require_once('../Classes/User.php');
require_once('../Classes/Payment.php');

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

if (!isset($_GET['reservation_id']) || !isset($_GET['total'])) {
    die("Reservation ID and total amount are required.");
}
$reservation_id = $_GET['reservation_id'];
$total = $_GET['total'];

$database = new Database();
$conn = $database->connect();

$userDetails = User::getUserDetailsById($conn, $user_id);
if (!$userDetails) {
    die("User details not found.");
}

$reservationDetails = Reservation::getReservationDetailsById($conn, $reservation_id);
if (!$reservationDetails) {
    die("Reservation not found.");
}

$journeyDetails = Journey::getJourneyDetailsById($conn, $reservationDetails['journeyid']);
if (!$journeyDetails) {
    die("Journey details not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_payment') {
    $payment = new Payment();
    $payment->setReservationId($reservation_id);
    $payment->setAmount($total);
    $payment->setPaymentMethod($_POST['payment_method']);

    if ($payment->createPayment()) {
        $reservation = new Reservation($user_id);
        $reservation->setReserverId($reservation_id);

        // Update reservation status based on payment method
        if ($_POST['payment_method'] === 'card') {
            $reservation->updateStatus('completed');
            echo "<script>alert('Payment successful and reservation updated!');</script>";
        } else {
            $reservation->updateStatus('pending');
            echo "<script>alert('Pay later request processed successfully!');</script>";
        }

        echo "<script>window.location.href = 'myJourney.php';</script>";
    } else {
        echo "<script>alert('Failed to create payment');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Now</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
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
                        <a class="nav-link active" href="reservation.php">reservation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="myJourney.php">My Journeys</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Log out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="bg"></div>
    <div class="payment-container">
        <h2 class="text-center">Payment Now</h2>
        <p class="text-center"><strong>Booking Details</strong></p>

        <div class="mb-3">
            <strong>Name:</strong> <?php echo $userDetails['firstname'] . ' ' . $userDetails['lastname']; ?><br>
            <strong>Contact:</strong> <?php echo $userDetails['contactnumber']; ?><br>
        </div>
        <div class="d-flex">
            <div class="mb-3 me-5" id="reservation-summary">
                <strong>Selected Seats:</strong> <span id="selected-seats"><?php echo $reservationDetails['seats']; ?></span><br>
                <strong>Charge:</strong> LKR. 100 <br>
                <strong>Total Amount:</strong> LKR. <span id="total-amount"><?php echo $total; ?></span>
            </div>
            <div class="mb-3" id="journey-details">
                <strong>Route:</strong> <span id="journey-route"><?php echo $journeyDetails['route']; ?></span><br>
                <strong>Departure Time:</strong> <span id="departure-time"><?php echo $journeyDetails['departure_time']; ?></span><br>
                <strong>Departure Date:</strong> <span id="departure-date"><?php echo $journeyDetails['date']; ?></span><br>
            </div>
        </div>

        <p class="text-center">Enter your card details to complete the payment.</p>

        <form id="payment-form">
            <div id="card-element" class="card-element"></div>
            <div class="text-center mt-4">
                <button id="submit-button" class="btn btn-primary me-5">Pay Now</button>
                <button id="pay-later" class="btn btn-primary">Pay Later</button>
            </div>
        </form>
        <div class="text-center">
            <button onclick="goBack()" class="btn btn-primary mt-3">Back to Reservation</button>
        </div>

        <div id="payment-message" class="mt-3 text-center" style="display: none;"></div>
    </div>

    <script>
    window.jsPDF = window.jspdf.jsPDF;

    const stripe = Stripe('pk_test_51PqSjVImplri7jqlTJvyrgv9SiAd8Vwv8Gecs8xBoF7Xr5WL1LPtUoVz87nNx9xzFqxKQ8CW15HsjgoA2sMzX9qA00AAJHIlHE');
    
    
    const elements = stripe.elements();
    const cardElement = elements.create('card');
    cardElement.mount('#card-element');
    
    const paymentForm = document.getElementById('payment-form');
    const paymentMessage = document.getElementById('payment-message');
    const submitButton = document.getElementById('submit-button');
    const payLaterButton = document.getElementById('pay-later');
    
    const name = "<?php echo $userDetails['firstname'] . ' ' . $userDetails['lastname']; ?>";
    const contact = "<?php echo $userDetails['contactnumber']; ?>";
    const route = "<?php echo $journeyDetails['route']; ?>";
    const departureTime = "<?php echo $journeyDetails['departure_time']; ?>";
    const departureDate = "<?php echo $journeyDetails['date']; ?>";
    const seats = "<?php echo $reservationDetails['seats']; ?>";
    const cost = 100;
    const totalAmount = "<?php echo $total; ?>";
    const reservation_id = "<?php echo $reservation_id; ?>";

    async function generateReceipt(type) {
        try {
            const pdf = new jsPDF();
            
            paymentMessage.textContent = 'Generating receipt...';
            paymentMessage.style.display = 'block';

            // Add Logo
        const logoURL = '../img/Bus Easy.png';
        try {
            pdf.addImage(logoURL, 'PNG', 80, 10, 50, 20);   //80 → X-coordinate (horizontal position from the left) 10 → Y-coordinate (vertical position from the top) 50 → Width of the image in the PDF 20 → Height of the image in the PDF
        } catch (logoError) {
            console.error("Logo loading error:", logoError);
            // Continue without logo
        }
            
            // Add Title
            pdf.setFontSize(18);
            pdf.setFont("Helvetica", "bold");
            pdf.text("E-Receipt", 105, 40, null, null, "center");
    
            // Horizontal Line Below Title
            pdf.setLineWidth(0.5);
            pdf.line(20, 45, 190, 45);
    
            // Booking and Bus Details
            pdf.setFontSize(12);
            pdf.setFont("Helvetica", "normal");
            pdf.text(`Name: ${name}`, 20, 55);
            pdf.text(`Contact: ${contact}`, 20, 65);
            pdf.text(`Payment Status: ${type}`, 20, 75);
            pdf.text(`Route: ${route}`, 140, 55);
            pdf.text(`Departure time: ${departureTime}`, 140, 65);
            pdf.text(`Departure Date: ${departureDate}`, 140, 75);
    
            // Horizontal Line Below Bus Details
            pdf.line(20, 85, 190, 85);
    
            // Booking Summary
            pdf.text("Selected Seats:", 20, 95);
            pdf.text(`${seats}`, 160, 95);
            pdf.text("Reservation Cost:", 20, 105);
            pdf.text(`LKR ${cost}`, 160, 105);
    
            // Horizontal Line Below Booking Summary
            pdf.line(20, 115, 190, 115);
    
            // Total Amount
            pdf.setFont("Helvetica", "bold");
            pdf.text("Total Amount:", 20, 125);
            pdf.text(`LKR ${totalAmount}`, 160, 125);
    
            // Horizontal Line Below Total
            pdf.setLineWidth(0.8);
            pdf.line(20, 135, 190, 135);
    
            // Thank You Message
            pdf.setFont("Helvetica", "italic");
            pdf.setFontSize(14);
            pdf.setTextColor(0, 102, 204);
            pdf.text("Thank you for booking with Bus Easy!", 105, 145, null, null, "center");
    
            // Footer
            pdf.setFontSize(10);
            pdf.setFont("Helvetica", "normal");
            pdf.setTextColor(0, 0, 0);
            pdf.text("Safe Journey", 105, 165, null, null, "center");
            pdf.text("Copyrights - Bus Easy", 105, 175, null, null, "center");
    
            pdf.save("e-receipt.pdf");
            paymentMessage.textContent = 'Receipt generated successfully!';
            paymentMessage.style.color = 'green';
        } catch (error) {
            console.error("PDF Generation Error:", error);
            paymentMessage.textContent = 'Failed to generate receipt. Please try again.';
            paymentMessage.style.color = 'red';
        }
    }

    function createPayment(payment_method) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="create_payment">
        <input type="hidden" name="payment_method" value="${payment_method}">
        <input type="hidden" name="reservation_id" value="${reservation_id}">
    `;
    document.body.appendChild(form);
    form.submit();
}
     //Template literals allow you to easily create multiline strings without needing to concatenate or use escape characters for newlines.


    // Event listener for "Pay Now" button
submitButton.addEventListener('click', async (e) => {
    e.preventDefault();
    submitButton.disabled = true;
    submitButton.textContent = 'Processing...';

    try {
        const { error, paymentMethod } = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
        });

        if (error) {
            throw error;
        }

        paymentMessage.textContent = 'Payment successful! Generating receipt...';
        paymentMessage.style.display = 'block';
        paymentMessage.style.color = 'green';

        await generateReceipt("paid");
        await createPayment("card"); // Pass "card" as the payment method

        submitButton.textContent = 'Success';
    } catch (err) {
        console.error("Payment Error:", err);
        paymentMessage.textContent = err.message || 'An error occurred. Please try again.';
        paymentMessage.style.display = 'block';
        paymentMessage.style.color = 'red';
        submitButton.disabled = false;
        submitButton.textContent = `Pay Rs. ${totalAmount}`;
    }
});

// Event listener for "Pay Later" button
payLaterButton.addEventListener('click', async (e) => {
    e.preventDefault();
    try {
        paymentMessage.textContent = 'Processing pay later request...';
        paymentMessage.style.display = 'block';

        await generateReceipt("Pending");
        await createPayment("cash"); // Pass "cash" as the payment method

        paymentMessage.textContent = 'Pay later request processed successfully!';
        paymentMessage.style.color = 'blue';
    } catch (err) {
        console.error("Pay Later Error:", err);
        paymentMessage.textContent = 'Failed to process pay later request. Please try again.';
        paymentMessage.style.color = 'red';
    }
});

    function goBack() {
        window.location.href = "reservation.php";
    }

    
    </script>
    <script>
        const stripe = Stripe('pk_test_51PqSjVImplri7jqlTJvyrgv9SiAd8Vwv8Gecs8xBoF7Xr5WL1LPtUoVz87nNx9xzFqxKQ8CW15HsjgoA2sMzX9qA00AAJHIlHE');
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>