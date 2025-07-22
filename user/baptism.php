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
    $created_at = date('Y-m-d H:i:s'); // PH timezone timestamp

    $firstname   = $_POST['firstname'];
    $lastname    = $_POST['lastname'];
    $middlename    = $_POST['middlename'];
    $birthdate   = $_POST['birthdate'];
    $gender      = $_POST['gender'];
    $birthplace  = $_POST['birthplace'];
    $book_no     = $_POST['book_no'];
    $suffix      = $_POST['suffix'] ?? '';
    $page_no     = $_POST['page_no'];

    $last_book_no = $book_no;
    $last_page_no = $page_no;
    // Insert into baptism_tbl
    $stmt = $conn->prepare("INSERT INTO baptism_tbl 
        (firstname,middlename, lastname, suffix, birthdate, gender, birthplace, book_no, page_no)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $firstname, $middlename, $lastname, $suffix, $birthdate, $gender, $birthplace, $book_no, $page_no);

    if ($stmt->execute()) {
        // Insert into bookmarks WITH manual timestamp
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
    <link rel="stylesheet" href="styles.css"> <!-- Optional if you have a shared style -->
</head>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        <?php include 'navbar.php'; ?>

        <div class="content-wrapper" style="margin-top: 20px;">
            <div class="container">
                <h2 class="my-4" style="text-align: left; font-weight: bold; font-size: 30px;">
                    Baptism Record Form
                </h2>


                <?php if ($error): ?>
                    <div class="alert alert-danger text-center"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" id="baptismForm">
                    <!-- Book No and Page No at the top -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label for="book_no" class="form-label">Book No</label>
                            <input type="text" name="book_no" id="book_no" class="form-control" required
                                value="<?= htmlspecialchars($last_book_no) ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="page_no" class="form-label">Page No</label>
                            <input type="text" name="page_no" id="page_no" class="form-control" required
                                value="<?= htmlspecialchars($last_page_no) ?>">
                        </div>

                    </div>

                    <!-- Rest of the fields start in a new row -->
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="firstname" class="form-label">First Name</label>
                            <input type="text" name="firstname" id="firstname" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label for="middlename" class="form-label">Middle Name</label>
                            <input type="text" name="middlename" id="middlename" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input type="text" name="lastname" id="lastname" class="form-control">
                        </div>

                        <div class="col-md-2">
                            <label for="suffix" class="form-label">Suffix</label>
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
                            <label for="birthdate" class="form-label">Birthdate</label>
                            <input type="date" name="birthdate" id="birthdate" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label for="gender" class="form-label">Gender</label>
                            <select name="gender" id="gender" class="form-select">
                                <option value="">-- Select Gender --</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="birthplace" class="form-label">Birthplace</label>
                            <input type="text" name="birthplace" id="birthplace" class="form-control">
                        </div>

                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary px-5" id="submitBtn">
                                <i class="bi bi-save me-2"></i>
                                <span id="btnText">Save Record</span>
                                <span id="btnLoading" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                </div>
                <div class="modal-body text-center">
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