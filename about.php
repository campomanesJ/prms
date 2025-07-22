<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About | Parish Matalom Records Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="about.css">

</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fs-4" href="index.php">Parish Matalom</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto navbar-nav">
                    <a class="nav-link ms-2" href="index.php">Home</a>
                    <a class="nav-link ms-2" href="searchRecord.php">Search Record</a>
                    <a class="nav-link ms-2" href="announcement.php">Announcements & Events</a>
                    <a class="nav-link ms-2 active" href="#">About</a>
                    <a class="nav-link ms-2 btn btn-warning border border-white" href="login/login.php">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-content">
            <h1>Saint Joseph Parish Church, Matalom</h1>
            <p>
                Established in the 1800s, Saint Joseph Parish Church stands as a spiritual beacon and a symbol of Matalom's rich cultural heritage. Its iconic bell tower and sea-side setting make it a sanctuary of faith and tradition.
            </p>
        </div>
    </div>

    <!-- Gallery Section -->
    <div class="gallery">
        <div class="container">
            <h2>Discover More of Saint Joseph Parish Church</h2>
            <div class="row g-4">
                <div class="col-md-6"><img src="assets/img/church2.jpg" class="img-fluid rounded shadow" /></div>
                <div class="col-md-6"><img src="assets/img/church3.jpg" class="img-fluid rounded shadow" /></div>
                <div class="col-md-6"><img src="assets/img/church4.png" class="img-fluid rounded shadow" /></div>
                <div class="col-md-6"><img src="assets/img/church5.png" class="img-fluid rounded shadow" /></div>
            </div>
        </div>
    </div>

    <!-- History Section -->
    <div class="history-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our History & Heritage</h2>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-icon"><i class="fas fa-cross"></i></div>
                    <div class="timeline-content bg-white p-4 rounded shadow">
                        <h5><strong>Early Origins (1843)</strong></h5>
                        <p>Founded under Fr. Leonardo Celes Diaz (1843–1883), who rests in the churchyard. Initially centered in Cahagnaan before moving by the Matalom River.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="timeline-content bg-white p-4 rounded shadow">
                        <h5><strong>Fortress Church</strong></h5>
                        <p>Built with coral stone by diocesan clergy, designed to protect locals from Moro pirate raids.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-icon"><i class="fas fa-church"></i></div>
                    <div class="timeline-content bg-white p-4 rounded shadow">
                        <h5><strong>Parish Establishment (1861)</strong></h5>
                        <p>Became an official parish on March 14, 1861 under St. Joseph following municipal organization in 1860.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-icon"><i class="fas fa-archway"></i></div>
                    <div class="timeline-content bg-white p-4 rounded shadow">
                        <h5><strong>Architectural Highlights</strong></h5>
                        <p>Four engaged columns, papal tiara and keys, and a separate octagonal bell tower with inverted “T” layout seen from above.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-icon"><i class="fas fa-star"></i></div>
                    <div class="timeline-content bg-white p-4 rounded shadow">
                        <h5><strong>Cultural Significance</strong></h5>
                        <p>Featured in top church guides and designated a Jubilee Church for 500 Years of Christianity. Part of Diocese of Maasin’s “seven stone churches.”</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-icon"><i class="fas fa-calendar-day"></i></div>
                    <div class="timeline-content bg-white p-4 rounded shadow">
                        <h5><strong>Feast & Services</strong></h5>
                        <p>Feast of St. Joseph on May 27, with daily early masses and multiple Sunday services.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Map Section -->
    <div class="map-section">
        <div class="container">
            <h2>Visit Us</h2>
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1004990.8671836308!2d124.78724813577232!3d10.282072276740475!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33076a1736d3cf5b%3A0x2616d8f3a9428c73!2sSaint%20Joseph%20Parish%20Church!5e0!3m2!1sen!2sph!4v1752546607717!5m2!1sen!2sph" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button id="backToTop" title="Go to top">▲</button>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show/hide back to top
        const backToTop = document.getElementById("backToTop");
        window.onscroll = () => {
            if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
                backToTop.style.display = "block";
            } else {
                backToTop.style.display = "none";
            }
        };
        backToTop.onclick = () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        };
    </script>
</body>

</html>