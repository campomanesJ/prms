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
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.55)),
                url('assets/img/church.jpg') center center / cover no-repeat;
            background-attachment: fixed;
            height: 90vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #fff;
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: 700;
        }

        .announcement-container {
            padding: 4rem 1rem;
        }

        .card {
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.12);
        }

        .card-img-top {
            height: 250px;
            object-fit: cover;
            cursor: pointer;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #212529;
        }

        .card-text {
            color: #495057;
            font-size: 1rem;
        }

        .card-footer {
            background: #fff;
            font-size: 0.85rem;
            color: #6c757d;
            border-top: none;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .card-img-top {
                height: 180px;
            }
        }

        /* Modal Zoom Styles */
        .zoom-controls {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 10;
        }

        .zoom-controls button {
            margin: 0 4px;
            font-size: 1.25rem;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background-color: rgba(255, 255, 255, 0.85);
            color: #000;
            font-weight: bold;
        }

        .modal-body {
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .zoomable-img {
            transition: transform 0.3s ease;
            transform-origin: center center;
            max-width: 100%;
            max-height: 80vh;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
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
                    <a class="nav-link ms-2" href="index.php">Home</a>
                    <a class="nav-link ms-2" href="searchRecord.php">Search Record</a>
                    <a class="nav-link ms-2 active" href="#">Announcements & Events</a>
                    <a class="nav-link ms-2" href="about.php">About</a>
                    <a class="nav-link ms-2 btn btn-warning border border-white" href="login/login.php">Login</a>
                </div>
            </div>
        </div>
    </nav>


    <!-- Hero Section -->
    <div class="hero">
        <h1>Announcements & Events</h1>
    </div>

    <!-- Announcements Section -->
    <div class="announcement-container container px-4">
        <div class="row justify-content-center">
            <?php if (count($announcements) > 0): ?>
                <?php foreach ($announcements as $index => $announcement): ?>
                    <div class="col-xl-8 col-lg-10 col-md-11 mb-5">
                        <div class="card">
                            <?php if (!empty($announcement['image_path'])): ?>
                                <img src="admin/<?= htmlspecialchars($announcement['image_path']) ?>" class="card-img-top rounded-top" alt="Announcement Image" data-bs-toggle="modal" data-bs-target="#imageModal<?= $index ?>">
                            <?php endif; ?>
                            <div class="card-body p-4">
                                <h3 class="card-title"><?= htmlspecialchars($announcement['title']) ?></h3>
                                <p class="card-text" style="white-space: pre-line;"><?= htmlspecialchars($announcement['description']) ?></p>
                            </div>
                            <div class="card-footer text-center text-muted py-3 bg-transparent border-0">
                                Posted on <?= isset($announcement['created_at']) ? date("F j, Y", strtotime($announcement['created_at'])) : '' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for Image -->
                    <?php if (!empty($announcement['image_path'])): ?>
                        <div class="modal fade" id="imageModal<?= $index ?>" tabindex="-1" aria-labelledby="imageModalLabel<?= $index ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content bg-dark">
                                    <div class="modal-header border-0">
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-0">
                                        <div class="zoom-controls">
                                            <button onclick="zoomIn(<?= $index ?>)">+</button>
                                            <button onclick="zoomOut(<?= $index ?>)">−</button>
                                        </div>
                                        <img src="admin/<?= htmlspecialchars($announcement['image_path']) ?>" id="zoom-img-<?= $index ?>" class="modal-img zoomable-img" alt="Announcement Full Image">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        No announcements found at this time.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const zoomLevels = {};

        function zoomIn(index) {
            zoomLevels[index] = (zoomLevels[index] || 1) + 0.2;
            updateZoom(index);
        }

        function zoomOut(index) {
            zoomLevels[index] = Math.max(0.2, (zoomLevels[index] || 1) - 0.2);
            updateZoom(index);
        }

        function updateZoom(index) {
            const img = document.getElementById(`zoom-img-${index}`);
            if (img) {
                img.style.transform = `scale(${zoomLevels[index]})`;
            }
        }

        document.addEventListener('keydown', function(e) {
            const modal = document.querySelector('.modal.show');
            if (!modal) return;

            const img = modal.querySelector('.zoomable-img');
            const index = img?.id?.split('-').pop();
            if (!index) return;

            if (e.ctrlKey && (e.key === '+' || e.key === '=')) {
                e.preventDefault();
                zoomIn(index);
            } else if (e.ctrlKey && e.key === '-') {
                e.preventDefault();
                zoomOut(index);
            }
        });
    </script>
</body>

</html>