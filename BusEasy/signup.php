<?php
include 'Classes/User.php';  // Including the User class, which handles user-related functionalities

$errorMessage = '';  // Variable to store error messages
$successMessage = '';  // Variable to store success messages

// Check if the 'userid' cookie is set, which indicates the user is already logged in
if(isset($_COOKIE['userid'])) {
    // Store user details from cookies into the session
    $_SESSION['user_id'] = $_COOKIE['userid'];
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['role'] = $_COOKIE['role'];
    $role = $_SESSION['role'];  // Store the user's role (passenger or conductor)

    // Redirect the user based on their role
    if ($role == 'passenger') {
        header('Location: passenger/reservation.php');  // Redirect to passenger page
    } else if($role == 'conductor'){
        header('Location: conductor/reservations.php');  // Redirect to conductor page
    }
    exit();  // Exit to prevent further code execution
}

// Handle form submission when the form is posted (using POST method)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $fname = $_POST['first-name'];
    $lname = $_POST['last-name'];
    $role = $_POST['role']; 
    $contact = $_POST['contact'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if any field is empty
    if (empty($fname) || empty($lname) || empty($role) || empty($contact) || empty($username) || empty($password)) {
        $errorMessage = "All fields are required.";  // Set error message
    } 
    // Validate contact number format (must be 10 digits)
    elseif (!preg_match("/^[0-9]{10}$/", $contact)) {
        $errorMessage = "Contact number must be a valid 10-digit number.";  // Set error message
    } else {
        // Create a new User object with the form data
        $user = new User($fname, $lname, $contact, $role, $username, $password);
        
        // Call the register method to attempt user registration
        $result = $user->register();

        // Check if registration was successful
        if ($result === true) {
            // Registration successful
            $successMessage = "Registration successful! Redirecting to login...";  // Set success message
            header("refresh:2;url=login.php");  // Redirect to login page after 2 seconds
        } else {
            // Registration failed
            $errorMessage = $result;  // Set error message from the result
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/style1.css"> 
</head>
<body>
    <!-- Navbar Section -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="img/logo.png" alt="Logo" class="logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fa fa-bars" aria-hidden="true"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sign-Up Form Section -->
    <div id="login-section" class="section text-center mt-5">
        <div class="container">
            <form action="signup.php" method="POST" class="login-form p-4 mx-auto" id="signup-form">
                <h2 class="mb-4">Sign Up</h2>

                <!-- Display Error Message -->
                <?php if (!empty($errorMessage)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>

                <!-- Display Success Message -->
                <?php if (!empty($successMessage)) : ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $successMessage; ?>
                    </div>
                <?php endif; ?>

                <!-- Role Selection -->
                <div class="role-selection mb-4">
                    <label class="form-label fw-bold d-block mb-2">Who are you?</label>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" id="role-passenger" name="role" value="passenger" required>
                        <label for="role-passenger" class="form-check-label mt-1">Passenger</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" id="role-conductor" name="role" value="conductor" required>
                        <label for="role-conductor" class="form-check-label mt-1">Conductor</label>
                    </div>
                </div>

                <!-- Hidden Form Fields (appear after selecting a role) -->
                <div id="signup" style="display: none;">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="first-name" placeholder="Enter your first name">
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="last-name" placeholder="Enter your last name">
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="contact" placeholder="Enter your contact number">
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="username" placeholder="Enter your username">
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" name="password" placeholder="Enter your password">
                    </div>
                </div>

                <!-- Social Media Login Options -->
                <div style="display: flex; margin-right: auto;margin-left: auto;">
                    <div class=" d-flex ms-auto me-auto">
                        <p class="me-4">Sign Up with : </p>
                        <img src="img/google.png" class="btn btn-dark me-3" alt="" style="width: 50px;height: 40px;">
                        <img src="img/fb.png" class="btn btn-dark" alt="" style="width: 50px;height: 40px;">
                    </div>
                </div>

                <!-- Login Link -->
                <div class="mt-3">
                    <p>Already have an account? <a href="login.php" class="login-link text-dark fw-bold text-decoration-none">Login</a></p>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100 mt-3">Sign Up</button>
            </form>
        </div>
    </div>

    <!-- JavaScript for Showing Form Based on Role -->
    <script>
        const roleRadios = document.querySelectorAll('input[name="role"]');
        const signupForm = document.getElementById('signup');

        roleRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                signupForm.style.display = 'block';  // Show the hidden form when a role is selected
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

