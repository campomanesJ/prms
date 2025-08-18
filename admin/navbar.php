<?php
$current_page = basename($_SERVER['PHP_SELF']); // e.g. home.php, manage_users.php, etc.
?>
<style>
    .custom-dropdown {
        background-color: #a9a9a9 !important;
        border: none;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    }

    .custom-dropdown .dropdown-item {
        color: #fff !important;
        padding: 10px 20px;
    }

    .custom-dropdown .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.27) !important;
        color: #fff !important;
    }

    .sidebar-footer {
        position: absolute;
        bottom: 10px;
        width: 100%;
        padding: 5px;
        text-align: center;
        color: #fff;
        font-size: 18px;
    }

    .nav-link.active {
        background-color: #007bff !important;
        color: #fff !important;
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link text-white">
                <i class="fa-solid fa-clock"></i> <span id="ph-time"></span>
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center text-white" id="navbarDropdown" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user fa-fw mr-1"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="change_password.php"><i class="fa-solid fa-user-gear"></i> Change Password</a></li>

                <li><a class="dropdown-item" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="" class="brand-link hover">
        <img src="assets/img/loginlogo.png" alt="icon" class="brand-image img-circle elevation-3">
        <span class="brand-text font-weight-light">PRMS-Matalom</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-header">Main</li>
                <li class="nav-item">
                    <a href="home.php" class="nav-link <?= ($current_page == 'home.php') ? 'active' : '' ?>">
                        <i class="fa-solid fa-house nav-icon"></i>
                        <p>Home</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage_users.php" class="nav-link <?= ($current_page == 'manage_users.php') ? 'active' : '' ?>">
                        <i class="fa-solid fa-users"></i>
                        <p>Users</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="parish_staff.php" class="nav-link <?= ($current_page == 'parish_staff.php') ? 'active' : '' ?>">
                        <i class="fa-solid fa-users"></i>
                        <p>Parish Staff</p>
                    </a>
                </li>

                <li class="nav-header">Others</li>
                <li class="nav-item position-relative">
                    <a href="view_data.php" class="nav-link position-relative <?= ($current_page == 'view_data.php') ? 'active' : '' ?>">
                        <i class="fa-solid fa-comments"></i>
                        <p class="d-inline">View Data</p>

                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage_announcement.php" class="nav-link <?= ($current_page == 'manage_announcement.php') ? 'active' : '' ?>">
                        <i class="fa-solid fa-bullhorn"></i>
                        <p>Manage Announcement</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<script>
    function updatePhilippineTime() {
        let options = {
            timeZone: 'Asia/Manila',
            hour12: true,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        let phTime = new Intl.DateTimeFormat('en-US', options).format(new Date());
        document.getElementById('ph-time').textContent = phTime + " PST";
    }
    setInterval(updatePhilippineTime, 1000);
    updatePhilippineTime();
</script>