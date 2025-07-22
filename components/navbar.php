<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<style>
    .nav-link {
        padding: 0.6rem 1.2rem;
        border-radius: 0.375rem;
        transition: background-color 0.3s, color 0.3s;
    }

    .nav-link.active {
        background-color: #ffc107 !important;
        color: #000 !important;
        font-weight: 600;
    }

    .navbar-nav .nav-item:not(:last-child) {
        margin-right: 1.5rem;
    }

    .navbar {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Parish Records</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'baptism.php' ? 'active' : '' ?>" href="baptism.php">Baptism</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'confirmation.php' ? 'active' : '' ?>" href="confirmation.php">Confirmation</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'marriage.php' ? 'active' : '' ?>" href="marriage.php">Marriage</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'death.php' ? 'active' : '' ?>" href="death.php">Death</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
