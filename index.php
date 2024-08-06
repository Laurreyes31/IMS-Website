<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salfo Technologies Home - IMS</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        .navbar {
            background-color: #DE2910;
        }

        .navbar .nav-link {
            color: white !important;
        }

        .hero-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('images/homepage.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
            border: 2px solid black;
        }

        .hero-section h1 {
            font-size: 4rem;
            font-weight: 700;
        }

        .hero-section h1 img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 3px solid black;
        }

        .hero-section p {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .features-section {
            padding: 60px 0;
            text-align: center;
        }

        .features-section .feature {
            margin-bottom: 30px;
        }

        .features-section .feature img {
            width: 60px;
            height: 60px;
            margin-bottom: 20px;
        }

        .navbar-brand img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid black;
        }

        .notify-section {
            background-color: #f8f9fa;
            padding: 60px 0;
            text-align: center;
        }

        .notify-section form {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .notify-section input[type="email"] {
            border-radius: 0;
            border-right: none;
        }

        .notify-section button {
            border-radius: 0;
            background-color: #DE2910;
            color: white;
            border: none;
        }

        .footer {
            background-color: #333;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand text-white" href="#">
            <img src="icons/wordlesslogo.webp" alt="Logo">
            Salfo Technologies
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="hero-section">
        <h1><img src="icons/logo.jpg" alt="Logo"></h1>
        <p>INVENTORY MANAGEMENT SYSTEM</p>
        <p>Track your goods throughout your entire supply chain, from purchasing to production to end sales</p>
    </div>

    <div class="container features-section">
        <div class="row">
            <div class="col-md-4 feature">
                <img src="icons/security_icon.png" alt="Seamless Design Icon">
                <h3>Data Security</h3>
                <p>Utilizes cybersecurity best practices to ensure IMS database security.</p>
            </div>
            <div class="col-md-4 feature">
                <img src="icons/sql_icon.png" alt="One to Many SQL Icon">
                <h3>One to Many SQL</h3>
                <p>Handles a multitude of SQL databases without sacrificing ease of use.</p>
            </div>
            <div class="col-md-4 feature">
                <img src="icons/beginner_icon.png" alt="Beginner Friendly Icon">
                <h3>Beginner Friendly</h3>
                <p>Easy to pick up, you'll be able to master this system within the day!</p>
            </div>
        </div>
    </div>

    <div class="notify-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2>About Us</h2>
                    <p>Our name is Salfo Technologies, where we strive to create new innovative products that seek to transform the
                        way we live in our society. We opened our doors in Hong Kong during 2018, and we have exploded in popularity 
                        ever since. We are currently working with our industry leaders such as Apple and Nvidia to deliver products
                        of the utmost quality to our customers. We hope you have the same ideals to us and we welcome you to the team!
                        <br>
                        <br>
                        Please click the video on the right to learn how to operate this IMS and the features it provides.
                    </p>
                </div>
                <div class="col-md-6">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/hUfXXwXNcBQ?si=6T0UdkgrrVMpxGYN" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2024 Salfo Technologies. All rights reserved.</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
