<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>St. Joseph Parish Records Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body,
        html {
            height: 100%;
            font-family: 'Poppins', sans-serif;
        }

        .hero {
            background: url('assets/img/church.jpg') center center / cover no-repeat;
            position: relative;
            height: 100vh;
            color: #fff;
        }

        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
        }

        .hero-content {
            position: relative;
            z-index: 1;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            padding: 0 15px;
        }

        h1 {
            font-size: 3rem;
        }

        @media (max-width:768px) {
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fs-4" href="index.php">
            <img src="assets/img/loginlogo.png" alt="Logo" height="40" class="me-2" />
            <div class="d-flex flex-column lh-1">
                <span class="text-white fw-bold mb-1">St. Joseph Parish</span>
                <small class="text-white-50" style="font-size: 0.85rem;">Matalom, Leyte</small>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="ms-auto navbar-nav">
                <a class="nav-link ms-2 active" href="#">Home</a>
                <a class="nav-link ms-2" href="searchRecord.php">Search Record</a>
                <a class="nav-link ms-2" href="announcement.php">Announcements & Events</a>
                <a class="nav-link ms-2" href="about.php">About</a>
                <a class="nav-link ms-2 btn btn-warning border border-white" href="login/login.php">Login</a>
            </div>
        </div>
    </div>
</nav>



    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-content">
            <h1>Welcome to St. Joseph Parish Records Management System</h1>
            <!-- <p>Securely manage baptism, marriage, and other parish records with ease and reliability.</p> -->
            <p>Securely manage certificates like baptism, marriage, and other parish records with ease and reliability. The system is currently under development, with a target completion date of December 31, 2025</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>