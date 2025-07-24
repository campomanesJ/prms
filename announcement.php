<?php
include 'db_connect.php';

// fetch announcements
$announcements = [];
$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Announcements & Events | St. Joseph Parish</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body,
        html {
            font-family: 'Poppins', sans-serif;
            background: #f7f7f7;
        }

        .hero {
            background: url('assets/img/church.jpg') center center / cover no-repeat;
            height: 50vh;
            position: relative;
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
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fs-4" href="index.php">St. Joseph Parish</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto navbar-nav">
                    <a class="nav-link ms-2" href="index.php">Home</a>
                    <a class="nav-link ms-2" href="searchRecord.php">Search Record</a>
                    <a class="nav-link ms-2 active" href="#">Announcements & Events</a>
                    <a class="nav-link ms-2" href="about.php">About</a>
                    <a class="nav-link ms-2 btn btn-warning border border-white" href="login/login.php">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="hero mt-5">
        <div class="hero-content">
            <h1>Announcements & Events</h1>
        </div>
    </div>

    <div class="container my-5">
        <?php if (count($announcements)): ?>
            <div class="row">
                <?php foreach ($announcements as $announcement): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <?php if (!empty($announcement['image_path'])): ?>
                                <img src="<?= htmlspecialchars($announcement['image_path']) ?>" class="card-img-top" alt="Announcement Image">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($announcement['title']) ?></h5>
                                <p class="card-text"><?= nl2br(htmlspecialchars($announcement['description'])) ?></p>
                            </div>
                            <div class="card-footer text-muted small">
                                Posted on <?= date('M d, Y', strtotime($announcement['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">No announcements found at this time.</div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>