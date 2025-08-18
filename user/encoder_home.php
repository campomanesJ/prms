<?php
include 'header.php';
include 'db_connect.php';

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

// Count total records in each table
function getTotal($conn, $table)
{
    $result = $conn->query("SELECT COUNT(*) as total FROM $table");
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

$total_baptism = getTotal($conn, 'baptism_tbl');
$total_confirmation = getTotal($conn, 'confirmation_tbl');
$total_death = getTotal($conn, 'death_tbl');
$total_marriage = getTotal($conn, 'marriage_tbl');
?>

<head>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card-icon {
            font-size: 2.8rem;
        }

        .card {
            border-radius: 20px;
        }

        .card-title.custom-title {
            font-size: 25px;
            /* large text */
            font-weight: bold;
        }

        .card-text.custom-number {
            font-size: 2.5rem;
            /* even larger number */
            font-weight: bold;
        }

        .custom-title,
        .custom-number {
            text-align: center;
            display: block;
            width: 100%;
        }


        @media (max-width: 576px) {
            .card-title {
                font-size: 1.1rem;
            }
        }
    </style>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include 'navbar.php'; ?>
        <div class="content-wrapper" style="margin-top: 20px;">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <h1 class="text-center fw-bold" style="font-size: 50px; font-weight: bold;">Welcome to Parish Records</h1>

                    <div class="container mt-5">
                        <div class="row g-4">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="card text-white bg-info h-100 rounded-4 shadow-sm">
                                    <div class="card-body text-center">

                                        <h5 class="card-title custom-title mt-2">Baptism Records</h5>
                                        <p class="card-text custom-number"><?= $total_baptism ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="card text-white bg-success h-100 rounded-4 shadow-sm">
                                    <div class="card-body text-center">

                                        <h5 class="card-title custom-title mt-2">Confirmation Records</h5>
                                        <p class="card-text custom-number"><?= $total_confirmation ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="card text-white bg-danger h-100 rounded-4 shadow-sm">
                                    <div class="card-body text-center">

                                        <h5 class="card-title custom-title mt-2">Death Records</h5>
                                        <p class="card-text custom-number"><?= $total_death ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="card text-white bg-warning h-100 rounded-4 shadow-sm">
                                    <div class="card-body text-center">

                                        <h5 class="card-title custom-title mt-2">Marriage Records</h5>
                                        <p class="card-text custom-number"><?= $total_marriage ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Latest Bookmark -->
                    <?php if ($latest_bookmark): ?>
                        <div class="card mt-5 mx-auto" style="max-width: 600px;">
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
        console.log("Logged in user ID: <?php echo htmlspecialchars($login_id); ?>");
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php include 'footer.php'; ?>