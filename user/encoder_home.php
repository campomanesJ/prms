<?php
include 'header.php';
include 'db_connect.php';

// Check if user is logged in by checking the session ID
if (!isset($_SESSION['login_id'])) {
    header("Location: ../index.php");
    exit;
}

$login_id = $_SESSION['login_id'];

// Get latest data for this user from bookmarks
$latest_bookmark = null;
$stmt = $conn->prepare("SELECT * FROM bookmarks WHERE user_id = ? ORDER BY timestamp DESC LIMIT 1");
$stmt->bind_param("i", $login_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $latest_bookmark = $result->fetch_assoc();
}
$stmt->close();
?>

<head>
    <link rel="stylesheet" href="styles.css">
    <style>
        .blinking {
            color: red;
            font-weight: bold;
            font-size: 100px;
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
                font-size: 30px;
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
                    <h1 class="text-center fw-bold" style="font-size: 50px; font-weight: bold;">Welcome to Parish Records</h1>

                    <div class="blinking mt-4">TEMPORARY!</div>

                    <div class="alert alert-info text-center mt-3 mx-auto" role="alert" style="max-width: 900px; font-size: 15px;">
                        This is a temporary data entry form. The system is currently under development, but all submitted data will still be inserted into the database. Please select a category from the navigation bar to proceed.
                    </div>

                    <?php if ($latest_bookmark): ?>
                        <div class="card mt-4 mx-auto" style="max-width: 600px;">
                            <div class="card-header bg-primary text-white">
                                Latest Entry Added by You
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars(ucfirst($latest_bookmark['type'])) ?> Record</h5>
                                <p class="card-text">
                                    <strong>Name:</strong> <?= htmlspecialchars($latest_bookmark['firstname'] . ' ' . $latest_bookmark['lastname']) ?><br>
                                    <strong>Book No:</strong> <?= htmlspecialchars($latest_bookmark['book_no']) ?><br>
                                    <strong>Page No:</strong> <?= htmlspecialchars($latest_bookmark['page_no']) ?><br>
                                    <strong>Added On:</strong> <?= htmlspecialchars(date('F j, Y g:i A', strtotime($latest_bookmark['timestamp']))) ?>
                                </p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-secondary text-center mt-4 mx-auto" style="max-width: 600px;">
                            You have not added any records yet.
                        </div>
                    <?php endif; ?>
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

    <script>
        // Print the user ID to the console
        console.log("Logged in user ID: <?php echo htmlspecialchars($login_id); ?>");
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php include 'footer.php'; ?>
