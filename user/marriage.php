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
    // Set timezone & create timestamp
    date_default_timezone_set('Asia/Manila');
    $created_at = date('Y-m-d H:i:s');

    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $middlename = $_POST['middlename'] ?? '';
    $suffix = $_POST['suffix'] ?? '';
    $husband_age = $_POST['husband_age'] ?? '';
    $husband_address = $_POST['husband_address'] ?? '';
    $husband_birthdate = $_POST['husband_birthdate'] ?? '';
    $book_no = $_POST['book_no'] ?? '';
    $page_no = $_POST['page_no'] ?? '';

    $last_book_no = $book_no;
    $last_page_no = $page_no;

    $husband_name = trim($firstname . ' ' . $middlename . ' ' . $lastname);

    // Insert into marriage_tbl
    $stmt = $conn->prepare("INSERT INTO marriage_tbl 
        (husband_name, suffix, husband_age, husband_address, husband_birthdate, book_no, page_no)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissss", $husband_name, $suffix, $husband_age, $husband_address, $husband_birthdate, $book_no, $page_no);

    if ($stmt->execute()) {
        // Also insert into bookmarks WITH manual timestamp
        $user_id = $_SESSION['login_id'];
        $type = 'marriage';

        $bookmark_stmt = $conn->prepare("INSERT INTO bookmarks 
            (user_id, firstname, lastname, book_no, page_no, type, timestamp)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $bookmark_stmt->bind_param("issssss", $user_id, $firstname, $lastname, $book_no, $page_no, $type, $created_at);
        $bookmark_stmt->execute();
        $bookmark_stmt->close();

        $success = "Marriage record saved successfully!";
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
    <meta charset="UTF-8" />
    <title>Marriage Record Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css">
</head>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        <?php include 'navbar.php'; ?>

        <div class="content-wrapper" style="margin-top: 20px;">
            <div class="container" style="margin-top: 0; padding-top: 0;">
                <h2 class="text-start fw-bold mb-4" style="font-size: 30px;">
                    Marriage Record Form
                </h2>


                <?php if ($error): ?>
                    <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" class="row g-3" id="marriageForm">
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
                    <div class="col-md-4">
                        <label for="firstname" class="form-label">Husband First Name</label>
                        <input type="text" name="firstname" id="firstname" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label for="middlename" class="form-label">Husband Middle Name</label>
                        <input type="text" name="middlename" id="middlename" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label for="lastname" class="form-label">Husband Last Name</label>
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

                    <div class="col-md-3">
                        <label for="husband_age" class="form-label">Husband Age</label>
                        <input type="number" name="husband_age" id="husband_age" class="form-control">
                    </div>

                    <div class="col-md-5">
                        <label for="husband_address" class="form-label">Husband Address</label>
                        <input type="text" name="husband_address" id="husband_address" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label for="husband_birthdate" class="form-label">Husband Birthdate</label>
                        <input type="date" name="husband_birthdate" id="husband_birthdate" class="form-control">
                    </div>


                    <div class="col-12 text-center mt-4">
                        <button type="submit" class="btn btn-success px-5" id="submitBtn">
                            <i class="bi bi-save me-2"></i>
                            <span id="btnText">Save Record</span>
                            <span id="btnLoading" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
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
                    <?= htmlspecialchars($success) ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const form = document.getElementById('marriageForm');
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
            setTimeout(() => successModal.hide(), 2000);
        <?php endif; ?>
    </script>

</body>

</html>