<?php
include 'header.php';
include 'db_connect.php';
?>

<head>
    <link rel="stylesheet" href="styles.css">
</head>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        <?php include 'navbar.php'; ?>

        <div id="dropdownMenu" class="dropdown-menu">
            <a href="home.php" id="home-link">Home</a>
            <a href="policy.php" id="policy-link">Policy</a>
            <a href="tourist.php" id="tourist-link">Tourist Spot</a>
        </div>
        <div class="content-wrapper" style="margin-top: 20px;">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Home</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="policy-tab" data-bs-toggle="tab" href="#policy" role="tab" aria-controls="policy" aria-selected="false">Policy</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="tourist-tab" data-bs-toggle="tab" href="#tourist" role="tab" aria-controls="tourist" aria-selected="false">Tourist Spot</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <h2>Welcome to Home</h2>
                    <p>Home content goes here...</p>
                </div>
                <div class="tab-pane fade" id="policy" role="tabpanel" aria-labelledby="policy-tab">
                    <?php include 'policy.php'; ?>
                </div>
                <div class="tab-pane fade" id="tourist" role="tabpanel" aria-labelledby="tourist-tab">
                    <?php include 'tourist_spot.php'; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleMenu() {
            var menu = document.getElementById("dropdownMenu");
            if (menu.style.display === "block") {
                menu.style.display = "none";
            } else {
                menu.style.display = "block";
            }
        }
    </script>
</body>
<?php include 'footer.php'; ?>
