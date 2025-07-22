<?php
include 'header.php';
include 'db_connect.php';

if (!isset($_SESSION['login_id'])) {
    header("Location: ../index.php");
    exit;
}
?>

<head>
    <link rel="stylesheet" href="styles.css">
    <style>
        .blinking {
            color: red;
            font-weight: bold;
            font-size: 150px;
            text-align: center;
            animation: blink 1s step-start 0s infinite;
        }

        @keyframes blink {
            50% {
                opacity: 0;
            }
        }

        @media (max-width: 768px) {
            .blinking {
                font-size: 40px;
            }
        }
    </style>
</head>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        <?php include 'navbar.php'; ?>
        <div class="content-wrapper" style="margin-top: 20px;">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <!-- Ensuring the text is bold -->
                    <h1 class="text-center fw-bold" style="font-size: 50px; font-weight: bold;">Welcome to Parish Records</h1>

                    <div class="blinking mt-4">TEMPORARY!</div>

                    <div class="alert alert-info text-center mt-3 mx-auto" role="alert" style="max-width: 900px; font-size: 20px;">
                        This is a temporary data entry form. The system is currently under development, but all submitted data will still be inserted into the database. Please select a category from the navigation bar to proceed.
                    </div>
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
</body>
<?php include 'footer.php'; ?>
