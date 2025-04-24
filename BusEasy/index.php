<?php
    include 'Classes/Contact.php';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $message = $_POST['message'];
        
        $contact = new Contact($name, $email, $message);
        if ($contact->create()) {
            echo "<script>alert('Message sent successfully');</script>";
        } else {
            echo "<script>alert('Message not sent');</script>";
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Easy - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="styles/style1.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg ">
        <div class="container-fluid">
            <!-- Brand Logo -->
            <a class="navbar-brand" href="#">
                <img src="img/logo.png" alt="Logo" class="logo">
            </a>
    
            <button class="moveTop" id="moveTop" onclick="moveTop()">↑</button>
            <!-- Toggler Button for Mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars" aria-hidden="true"></i>
            </button>
            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    

    <!-- Carousel -->
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="img/s1.png" class="img-fluid" alt="Bus Image 1">
                <div class="carousel-caption">
                    <h1>Welcome to Bus Easy</h1>
                    <h5>Your ultimate solution for bus seat reservations.</h5>
                    <p>Experience hassle-free travel planning with our user-friendly platform designed to make booking fast and convenient.</p>
                    <div class="carousel-buttons">
                        <a href="login.php" class="btn btn-dark">Login</a>
                        <a href="signup.php" class="btn btn-dark">Sign Up</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <img src="img/s2.png" class="d-block w-100" alt="Bus Image 2">
                <div class="carousel-caption d-md-block">
                    <h1>Comfortable Journeys</h1>
                    <h5>Travel in comfort and style with Bus Easy.</h5>
                    <p>Enjoy spacious seating, modern amenities, and a smooth journey. Your comfort is our top priority.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="img/s3.png" class="d-block w-100" alt="Bus Image 3">
                <div class="carousel-caption d-md-block">
                    <h1>Real-Time Notifications</h1>
                    <h5>Stay updated with our real-time alerts.</h5>
                    <p>Receive notifications about schedule changes, upcoming stops, and more, so you never miss a moment of your trip.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="img/s4.png" class="d-block w-100" alt="Bus Image 4">
                <div class="carousel-caption d-md-block">
                    <h1>Sit Easy on Crowded Days</h1>
                    <h5>Enjoy stress-free travel even when tickets are fully booked.</h5>
                    <p>Our smart reservation system ensures you find the best available seats and enjoy a hassle-free journey, even during peak travel times.</p>
                </div>
            </div>
        </div>
        
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <about-us id="about"></about-us>
    
    
    <services-section id="services"></services-section>

    
    <!-- Mission Section -->
    <div id="mission" class="section bg-black text-light">
        <div class="container">
            <div class="row align-items-center">
                <h2>Our Mission</h2>
                <!-- Left Side: Text -->
                <div class="col-md-6 text-md-start">
                    <p class="">
                        Our mission is to revolutionize bus travel by combining innovative technology with unparalleled customer service. 
                        We aim to make every journey seamless, comfortable, and stress-free.
                        we’re redefining how people experience bus travel, ensuring every trip is safe, reliable, and enjoyable.
                    </p>
                </div>
                <!-- Right Side: Photo -->
                <div class="col-md-6">
                    <img src="img/mission.png" alt="Our Mission" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>
    
    
    <!-- Contact Us Section -->
    <div id="contact" class="section text-center ">
        <div class="container">
            <h2 class="mb-4">Contact Us</h2>
            <p class="text-muted mb-4">We’d love to hear from you! Fill out the form below, and we’ll get back to you as soon as possible.</p>
            <form action="index.php" method="POST" class="all-form p-4 mx-auto">
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label fw-bold">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="5" placeholder="Write your message here" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Submit</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer text-white py-4">
        <div class="container text-center">
            <!-- Social Media Icons -->
            <div class="social-icons mb-3">
                <a href="https://facebook.com" target="_blank" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="https://instagram.com" target="_blank" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="https://twitter.com" target="_blank" class="social-icon"><i class="fab fa-twitter"></i></a>
                <a href="https://google.com" target="_blank" class="social-icon"><i class="fab fa-google"></i></a>
                <a href="https://youtube.com" target="_blank" class="social-icon"><i class="fab fa-youtube"></i></a>
            </div>
    
            <!-- Navigation Links -->
            <nav class="footer-links mb-3">
                <a href="#home" class="footer-link">Home</a>
                <a href="#about" class="footer-link">About</a>
                <a href="#services" class="footer-link">Services</a>
                <a href="#" class="footer-link">Login</a>
                <a href="#" class="footer-link">Sign Up</a>
            </nav>
    
            <!-- Address and Contact Details -->
            <div class="footer-details mb-3">
                <p class="mb-1">No.89 Highlevel Road, Maharagama</p>
                <p class="mb-1">Phone: +94 70 522 9031 | Email: info@buseasy.com</p>
            </div>
    
            <!-- Copyright -->
            <div class="footer-bottom">
                <p class="mb-0">&copy; 2025, Designed by Dulmini Kumari</p>
            </div>
        </div>
    </footer>
    <script src="js/about-us.js"></script>
    <script src="js/services.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function moveTop(){
            window.scrollTo({
                top: 0,
                behavior: 'smooth' 
            });
        }
        
    </script>
</body>
</html>
