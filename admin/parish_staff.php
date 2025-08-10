<?php
session_start();
include 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['login_id'])) {
    header("Location: ../index.php");
    exit;
}

// Handle insert POST
$alert = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])) {
    $role = $_POST['role'];
    $fname = trim($_POST['fname']);
    $mname = trim($_POST['mname']);
    $lname = trim($_POST['lname']);
    $username = trim($_POST['username']);
    $birthdate = $_POST['birthdate'];
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("INSERT INTO parish_staff (role, fname, mname, lname, username, birthdate, address, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssssssss", $role, $fname, $mname, $lname, $username, $birthdate, $address, $email);
        $stmt->execute();
        $stmt->close();
        $alert = '<div class="alert alert-success">Staff added successfully!</div>';
    } else {
        $alert = '<div class="alert alert-danger">Error adding staff.</div>';
    }
}

// Pagination & Search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM parish_staff WHERE fname LIKE ? OR lname LIKE ? OR role LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$searchParam = "%" . $search . "%";
$stmt->bind_param("sssii", $searchParam, $searchParam, $searchParam, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$staffs = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Count total for pagination
$countStmt = $conn->prepare("SELECT COUNT(*) as total FROM parish_staff WHERE fname LIKE ? OR lname LIKE ? OR role LIKE ?");
$countStmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$countStmt->close();

$totalPages = ceil($totalRows / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Parish Staff List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .content-wrapper {
            padding: 20px;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include 'navbar.php'; ?>

        <div class="content-wrapper">
            <div class="container-fluid">
                <h2 class="mb-4">Parish Staff List</h2>

                <?= $alert ?>

                <form method="get" id="filterForm" class="row g-3 align-items-center mb-4">
                    <div class="col-md-4">
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search by name or role" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-2">
                        <select name="limit" class="form-select" onchange="this.form.submit()">
                            <?php foreach ([5, 10, 25, 50] as $option): ?>
                                <option value="<?= $option ?>" <?= $limit == $option ? 'selected' : '' ?>><?= $option ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#addModal">+ Add Staff</button>
                    </div>
                </form>

                <?php if (count($staffs) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Role</th>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Age</th>
                                    <th>Birthdate</th>
                                    <th>Address</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($staffs as $row): ?>
                                    <?php
                                    $fullName = htmlspecialchars($row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname']);
                                    if (strtolower($row['role']) === 'priest') $fullName = 'Fr. ' . $fullName;

                                    $birthDate = new DateTime($row['birthdate']);
                                    $today = new DateTime();
                                    $age = $birthDate->diff($today)->y;

                                    $role = ucfirst($row['role']);
                                    if ($role === 'Priest') $role = 'Parish Priest';
                                    if ($role === 'Registrar') $role = 'Parish Registrar';
                                    ?>
                                    <tr>
                                        <td><?= $role ?></td>
                                        <td><?= htmlspecialchars($row['username']) ?></td>
                                        <td><?= $fullName ?></td>
                                        <td><?= $age ?></td>
                                        <td><?= htmlspecialchars($row['birthdate']) ?></td>
                                        <td><?= htmlspecialchars($row['address']) ?></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php else: ?>
                    <p class="text-muted text-center">No staff found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add Staff Modal -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-sm border-0">
                    <form method="post">
                        <input type="hidden" name="add_staff" value="1">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="addModalLabel">Add New Staff</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row g-3">

                                    <!-- Name Section -->
                                    <div class="col-12"><strong>Personal Information</strong></div>
                                    <div class="col-md-4">
                                        <input type="text" name="fname" id="fname" class="form-control" placeholder="First Name" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="mname" class="form-control" placeholder="Middle Name (optional)">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="lname" class="form-control" placeholder="Last Name" required>
                                    </div>

                                    <div class="col-md-6">
                                        <input type="date" name="birthdate" id="birthdate" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="age" id="age" class="form-control" placeholder="Age" readonly>
                                    </div>

                                    <hr class="mt-4 mb-2">

                                    <!-- Contact Section -->
                                    <div class="col-12"><strong>Contact Details</strong></div>
                                    <div class="col-md-6">
                                        <input type="text" name="address" class="form-control" placeholder="Address" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                                        <small id="emailFeedback" class="text-danger" style="display:none;"></small>
                                    </div>


                                    <hr class="mt-4 mb-2">

                                    <!-- Role and Account Section -->
                                    <div class="col-12"><strong>Account Information</strong></div>
                                    <div class="col-md-6">
                                        <select name="role" id="role" class="form-select" required>
                                            <option value="" disabled selected>Select Role</option>
                                            <option value="priest">Parish Priest</option>
                                            <option value="registrar">Parish Registrar</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="username" id="username" class="form-control" placeholder="Username" readonly required>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-plus-circle"></i> Add Staff
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <?php include 'footer.php'; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const role = document.getElementById('role');
        const fname = document.getElementById('fname');
        const username = document.getElementById('username');
        const birthdate = document.getElementById('birthdate');
        const age = document.getElementById('age');

        function updateUsername() {
            const fnameVal = fname.value.trim().toLowerCase();
            const roleVal = role.value;

            if (fnameVal && roleVal) {
                fetch(`generate_username.php?fname=${encodeURIComponent(fnameVal)}&role=${encodeURIComponent(roleVal)}`)
                    .then(response => response.text())
                    .then(data => {
                        username.value = data;
                    });
            } else {
                username.value = '';
            }
        }


        function calculateAge() {
            const bdate = new Date(birthdate.value);
            const today = new Date();
            if (!isNaN(bdate)) {
                let years = today.getFullYear() - bdate.getFullYear();
                const m = today.getMonth() - bdate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < bdate.getDate())) {
                    years--;
                }
                age.value = years;
            } else {
                age.value = '';
            }
        }

        role.addEventListener('change', updateUsername);
        fname.addEventListener('input', updateUsername);
        birthdate.addEventListener('change', calculateAge);
    });


    const emailInput = document.getElementById('email');
    const emailFeedback = document.getElementById('emailFeedback');
    const submitButton = document.querySelector('#addModal button[type="submit"]');

    emailInput.addEventListener('blur', () => {
        const email = emailInput.value.trim();
        if (email !== '') {
            fetch(`check_email.php?email=${encodeURIComponent(email)}`)
                .then(response => response.text())
                .then(result => {
                    if (result === 'exists') {
                        emailFeedback.style.display = 'block';
                        emailFeedback.textContent = 'This email is already registered.';
                        submitButton.disabled = true;
                    } else {
                        emailFeedback.style.display = 'none';
                        emailFeedback.textContent = '';
                        submitButton.disabled = false;
                    }
                });
        } else {
            emailFeedback.style.display = 'none';
            emailFeedback.textContent = '';
            submitButton.disabled = false;
        }
    });
</script>

</html>