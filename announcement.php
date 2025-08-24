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
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.55)),
                url('assets/img/church.jpg') center center / cover no-repeat;
            background-attachment: fixed;
            height: 100vh;
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

        .announcement-section {
            background: linear-gradient(180deg, #1f1d1dff 0%, #f0f9ff 100%);
            padding: 4rem 0;
        }

        .card {
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            height: 100%;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.12);
        }

        .card-img-top {
            height: 220px;
            object-fit: cover;
        }

        #backToTopBtn {
            position: fixed;
            bottom: 40px;
            right: 30px;
            z-index: 999;
            font-size: 22px;
            border: none;
            outline: none;
            background-color: #000;
            color: white;
            cursor: pointer;
            padding: 12px 16px;
            border-radius: 50%;
            display: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        #backToTopBtn:hover {
            background-color: #0b5ed7;
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
                    <a class="nav-link ms-2" href="index.php">Home</a>
                    <a class="nav-link ms-2" href="searchRecord.php">Search Record</a>
                    <a class="nav-link ms-2 active" href="#">Announcements & Events</a>
                    <a class="nav-link ms-2" href="about.php">About</a>
                    <a class="nav-link ms-2 btn btn-warning border border-white" href="login/login.php">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <div class="hero">
        <h1>Announcements & Events</h1>
    </div>

    <!-- Announcements -->
    <div class="announcement-section">
        <div class="container">
            <div class="row g-4">
                <?php if (count($announcements) > 0): ?>
                    <?php foreach ($announcements as $index => $announcement): ?>
                        <div class="col-lg-6 col-md-12">
                            <div class="card" onclick="showAnnouncement(<?= $index ?>)">
                                <?php if (!empty($announcement['image_path'])): ?>
                                    <img src="admin/<?= htmlspecialchars($announcement['image_path']) ?>" class="card-img-top" alt="Announcement Image">
                                <?php endif; ?>
                                <div class="card-body p-4">
                                    <h3 class="card-title"><?= htmlspecialchars($announcement['title']) ?></h3>
                                    <p class="card-text text-truncate"><?= htmlspecialchars($announcement['description']) ?></p>
                                </div>
                                <div class="card-footer text-center text-muted py-3 bg-transparent border-0">
                                    Posted on <?= date("F j, Y", strtotime($announcement['created_at'])) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Data for SweetAlert -->
                        <div id="announcement-data-<?= $index ?>" class="d-none"
                            data-title="<?= htmlspecialchars($announcement['title'], ENT_QUOTES) ?>"
                            data-description="<?= nl2br(htmlspecialchars($announcement['description'], ENT_QUOTES)) ?>"
                            data-image="<?= !empty($announcement['image_path']) ? 'admin/' . htmlspecialchars($announcement['image_path'], ENT_QUOTES) : '' ?>"
                            data-date="<?= date("F j, Y", strtotime($announcement['created_at'])) ?>">
                        </div>
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
    </div>

    <!-- Back to Top -->
    <button onclick="scrollToTop()" id="backToTopBtn" title="Back to top">▲</button>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // SweetAlert Preview
        function showAnnouncement(index) {
            const el = document.getElementById(`announcement-data-${index}`);
            if (!el) return;

            const title = el.dataset.title;
            const description = el.dataset.description;
            const image = el.dataset.image;
            const date = el.dataset.date;

            let htmlContent = `<p>${description}</p><div class="text-muted small mt-3">Posted on ${date}</div>`;
            if (image) {
                htmlContent = `<img src="${image}" alt="Announcement Image" class="img-fluid rounded mb-3"/>` + htmlContent;
            }

            Swal.fire({
                title: title,
                html: htmlContent,
                width: '60%',
                confirmButtonColor: '#0d6efd'
            });
        }

        // Back to Top
        const backToTopBtn = document.getElementById("backToTopBtn");
        window.onscroll = function() {
            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                backToTopBtn.style.display = "block";
            } else {
                backToTopBtn.style.display = "none";
            }
        };

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    </script>
</body>

</html>