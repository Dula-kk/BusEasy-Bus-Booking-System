<?php
session_start();

include 'Classes/User.php';
include 'Classes/Authenticate.php';

if(isset($_COOKIE['userid'])) {
    $_SESSION['user_id'] = $_COOKIE['userid'];
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['role'] = $_COOKIE['role'];
    $role = $_SESSION['role'];
    if ($role == 'passenger') {
        header('Location: passenger/reservation.php');

    } else if($role == 'conductor'){
        header('Location: conductor/reservations.php');
    }
    exit();
}
$auth = new Authenticate();
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $errorMessage = "Username and password are required.";
    } else {
        $user_data = $auth->login($username, $password);
        if ($user_data != false) {
            $_SESSION['user_id'] = $user_data['userid'];
            $_SESSION['username'] = $user_data['username'];
            $_SESSION['role'] = $user_data['role'];
            $role = $_SESSION['role'];
            setcookie('userid', $_SESSION['user_id'], time() + 60, '/');
            setcookie('username', $_SESSION['username'], time() + 60, '/');
            setcookie('role', $_SESSION['role'], time() + 60, '/');

            if ($role == 'passenger') {
                header('Location: passenger/reservation.php');

            } else if($role == 'conductor'){
                header('Location: conductor/reservations.php');
            }

            exit;
        } else {
            $errorMessage = "Invalid username or password.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/style1.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg ">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="img/logo.png" alt="Logo" class="logo">
            </a>
    
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars" aria-hidden="true"></i>
            </button>
            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="signup.php">Sign Up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="bg-img"></div>    
        <div id="login-section" class="section text-center">
            <div class="container">
                <form action="login.php" method="POST" class="login-form p-4 mx-auto">
                    <h2 class="mb-4">Login</h2>
                    <?php if (!empty($errorMessage)) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $errorMessage; ?>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="username" class="form-label fw-bold">User Name</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
                    </div>
                    <div class="mt-3">
                        <p>New User? <a href="signup.php" class="signup-link">Sign Up</a></p>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                    
                    <div style="display: flex; margin-right: auto;margin-left: auto;">
                        <div class=" d-flex ms-auto me-auto" >
                            <p class="me-4">Login with : </p>
                            <img src="img/google.png" class="btn btn-dark me-3" id="google-login" alt="" style="width: 50px;height: 40px;">
                            <img src="img/fb.png" class="btn btn-dark" id="facebook-login" alt="" style="width: 50px;height: 40px;">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <!-- Google API -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <!-- Facebook SDK -->
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
      
    <script>
        
        window.onload = function() {
            google.accounts.id.initialize({
                client_id: "", // add
                callback: handleGoogleLogin,
            });
            google.accounts.id.renderButton(
                document.getElementById("google-login"),
                { theme: "outline", size: "large" }
            );
        };

        function handleGoogleLogin(response) {
            console.log("Google login response:", response);
        }

        // Facebook Login Configuration
        window.fbAsyncInit = function() {
            FB.init({
                appId: '',  
                cookie: true,
                xfbml: true,
                version: 'v12.0',
            });
        };

        document.getElementById('facebook-login').addEventListener('click', function() {
            FB.login(function(response) {
                if (response.authResponse) {
                    console.log('Facebook login response:', response);
                } else {
                    console.log('User cancelled login or did not fully authorize.');
                }
            }, { scope: 'public_profile,email' });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
