<?php
include 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['login_id'])) {
    header("Location: ../index.php");
    exit;
}

$success = '';
$error = '';
$showModal = false;
$last_book_no = '';
$last_page_no = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    date_default_timezone_set('Asia/Manila');
    $created_at = date('Y-m-d H:i:s');

    $firstname   = $_POST['firstname'];
    $lastname    = $_POST['lastname'];
    $middlename  = $_POST['middlename'];
    $birthdate   = $_POST['birthdate'];
    $gender      = $_POST['gender'];
    $birthplace  = $_POST['birthplace'];
    $book_no     = $_POST['book_no'];
    $suffix      = $_POST['suffix'] ?? '';
    $page_no     = $_POST['page_no'];

    $last_book_no = $book_no;
    $last_page_no = $page_no;

    $stmt = $conn->prepare("INSERT INTO baptism_tbl 
        (firstname,middlename, lastname, suffix, birthdate, gender, birthplace, book_no, page_no)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $firstname, $middlename, $lastname, $suffix, $birthdate, $gender, $birthplace, $book_no, $page_no);

    if ($stmt->execute()) {
        $user_id = $_SESSION['login_id'];
        $type = 'baptism';

        $bookmark_stmt = $conn->prepare("INSERT INTO bookmarks 
            (user_id, firstname, lastname, book_no, page_no, type, timestamp)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $bookmark_stmt->bind_param("issssss", $user_id, $firstname, $lastname, $book_no, $page_no, $type, $created_at);
        $bookmark_stmt->execute();
        $bookmark_stmt->close();

        $success = "Record saved successfully!";
        $showModal = true;
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Baptism Records</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-custom {
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .form-label {
            font-weight: 500;
        }

        .form-control,
        .form-select {
            border-radius: 0.6rem;
            padding: 0.65rem 1rem;
        }

        .btn-primary {
            border-radius: 0.6rem;
            padding: 0.65rem 1.5rem;
            font-weight: 500;
        }

        .btn-primary:disabled {
            background-color: #6c757d;
            border: none;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include 'navbar.php'; ?>

        <div class="content-wrapper py-4">
            <div class="container">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-primary text-white rounded-top-4">
                        <h4 class="mb-0 fw-bold">
                            <i class="bi bi-droplet me-2"></i>Baptism Record Form
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger text-center"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST" id="baptismForm">
                            <!-- Book No & Page No -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <label for="book_no" class="form-label fw-semibold">Book No</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-book"></i></span>
                                        <input type="text" name="book_no" id="book_no" class="form-control" required
                                            value="<?= htmlspecialchars($last_book_no) ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="page_no" class="form-label fw-semibold">Page No</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-file-earmark-text"></i></span>
                                        <input type="text" name="page_no" id="page_no" class="form-control" required
                                            value="<?= htmlspecialchars($last_page_no) ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Name Fields -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label for="firstname" class="form-label fw-semibold">First Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" name="firstname" id="firstname" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="middlename" class="form-label fw-semibold">Middle Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-lines-fill"></i></span>
                                        <input type="text" name="middlename" id="middlename" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="lastname" class="form-label fw-semibold">Last Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                        <input type="text" name="lastname" id="lastname" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <!-- Suffix, Birthdate, Gender -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-2">
                                    <label for="suffix" class="form-label fw-semibold">Suffix</label>
                                    <select name="suffix" id="suffix" class="form-select">
                                        <option value="">None</option>
                                        <option value="Jr.">Jr.</option>
                                        <option value="Sr.">Sr.</option>
                                        <option value="II">II</option>
                                        <option value="III">III</option>
                                        <option value="IV">IV</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="birthdate" class="form-label fw-semibold">Birthdate</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                                        <input type="date" name="birthdate" id="birthdate" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="gender" class="form-label fw-semibold">Gender</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-gender-ambiguous"></i></span>
                                        <select name="gender" id="gender" class="form-select">
                                            <option value="">-- Select Gender --</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Birthplace -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="birthplace" class="form-label fw-semibold">Birthplace</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                        <input type="text" name="birthplace" id="birthplace" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-success px-5 rounded-pill" id="submitBtn">
                                    <i class="bi bi-save me-2"></i>
                                    <span id="btnText">Save Record</span>
                                    <span id="btnLoading" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title"><i class="bi bi-check-circle-fill me-2"></i>Success</h5>
                </div>
                <div class="modal-body text-center fs-5">
                    <?= $success ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const form = document.getElementById('baptismForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoading = document.getElementById('btnLoading');

        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            btnText.textContent = "Saving...";
            btnLoading.classList.remove("d-none");
        });

        <?php if ($showModal): ?>
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            setTimeout(() => {
                successModal.hide();
            }, 2000);
        <?php endif; ?>
    </script>
</body>

</html>