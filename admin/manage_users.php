<?php
include 'header.php';
include 'db_connect.php';
if (!isset($_SESSION['login_id'])) {
    header("Location: ../index.php");
    exit;
}

// --- Handle search & pagination ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Count total users
$countSql = "SELECT COUNT(*) as total FROM user 
             WHERE username LIKE ? OR fname LIKE ? OR lname LIKE ? OR email LIKE ?";
$stmt = $conn->prepare($countSql);
$like = "%$search%";
$stmt->bind_param("ssss", $like, $like, $like, $like);
$stmt->execute();
$totalRows = $stmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch users
$sql = "SELECT * FROM user 
        WHERE username LIKE ? OR fname LIKE ? OR lname LIKE ? OR email LIKE ?
        ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssii", $like, $like, $like, $like, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>


<head>
    <meta charset="UTF-8">
    <title>View Users Data</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        thead th {
            background: linear-gradient(90deg, #01255cff, #024d5cff);
            color: #fff !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 14px;
            border: none;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include 'navbar.php'; ?>
        <div class="content-wrapper">
            <div class="container-fluid mt-4">
                <div class="card shadow-lg">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold">Users List</h4>
                        <button class="btn btn-success btn-sm ms-auto" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            + Add User
                        </button>
                    </div>
                    <div class="card-body">

                        <form method="GET" id="filterForm" class="row g-2 mb-3">
                            <!-- Search Field -->
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search..."
                                    value="<?= htmlspecialchars($search) ?>">
                            </div>

                            <!-- Limit Dropdown -->
                            <div class="col-md-2">
                                <select name="limit" class="form-select">
                                    <option value="5" <?= $limit == 5 ? 'selected' : '' ?>>5</option>
                                    <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                                    <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25</option>
                                    <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                                </select>
                            </div>
                        </form>
                        <!-- Users table -->
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Age</th>
                                    <th>Birthdate</th>
                                    <th>Address</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= ucfirst(htmlspecialchars($row['role'])) ?></td>
                                            <td><?= htmlspecialchars($row['username']) ?></td>
                                            <td><?= htmlspecialchars($row['fname'] . ' ' . $row['lname']) ?></td>
                                            <td><?= htmlspecialchars($row['age']) ?></td>
                                            <td><?= htmlspecialchars($row['birthdate']) ?></td>
                                            <td><?= htmlspecialchars($row['address']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editUserModal"
                                                    data-id="<?= $row['id'] ?>"
                                                    data-role="<?= $row['role'] ?>"
                                                    data-username="<?= $row['username'] ?>"
                                                    data-fname="<?= $row['fname'] ?>"
                                                    data-lname="<?= $row['lname'] ?>"
                                                    data-age="<?= $row['age'] ?>"
                                                    data-birthdate="<?= $row['birthdate'] ?>"
                                                    data-address="<?= $row['address'] ?>"
                                                    data-email="<?= $row['email'] ?>">Edit</button>
                                                <?php if (strtolower($row['role']) !== 'admin'): ?>
                                                    <button class="btn btn-danger btn-sm deleteUserBtn" data-id="<?= $row['id'] ?>">Delete</button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No users found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <nav>
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $page - 1 ?>">Previous</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $page + 1 ?>">Next</a>
                                </li>
                            </ul>
                        </nav>

                    </div>
                </div>
            </div>
        </div>

        <!-- Auto-submit JS -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const filterForm = document.getElementById("filterForm");
                const searchInput = filterForm.querySelector("input[name='search']");
                const limitSelect = filterForm.querySelector("select[name='limit']");

                // Auto-submit on typing (with delay)
                let typingTimer;
                searchInput.addEventListener("keyup", function() {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => filterForm.submit(), 500);
                });

                // Auto-submit on limit change
                limitSelect.addEventListener("change", function() {
                    filterForm.submit();
                });
            });
        </script>

    </div>

    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form id="addUserForm" class="modal-content shadow-lg border-0 rounded-4">

                <!-- Header -->
                <div class="modal-header bg-success text-white rounded-top-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-person-plus me-2"></i> Add New User
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Role -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Role</label>
                            <select name="role" id="role" class="form-select shadow-sm" required>
                                <option value="admin">Admin</option>
                                <option value="encoder">Encoder</option>
                            </select>
                        </div>

                        <!-- Name -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">First Name</label>
                            <input type="text" name="fname" id="fname" class="form-control shadow-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Last Name</label>
                            <input type="text" name="lname" id="lname" class="form-control shadow-sm" required>
                        </div>

                        <!-- Birthdate + Age -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Birthdate</label>
                            <input type="date" name="birthdate" id="birthdate" class="form-control shadow-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Age</label>
                            <input type="number" name="age" id="age" class="form-control shadow-sm bg-light" readonly>
                        </div>

                        <!-- Address -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Address</label>
                            <input type="text" name="address" class="form-control shadow-sm" required>
                        </div>

                        <!-- Username -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" id="username" class="form-control shadow-sm bg-light" readonly>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="email" class="form-control shadow-sm" required>
                        </div>
                    </div>

                    <!-- Hidden default password -->
                    <input type="hidden" name="password" value="prms@matalom">
                </div>

                <!-- Footer -->
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" id="saveBtn" class="btn btn-success px-4 fw-bold" disabled>
                        <i class="bi bi-check-circle me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- Toast / Popup -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
        <div id="toastMessage" class="toast align-items-center text-white bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const fname = document.getElementById("fname");
            const lname = document.getElementById("lname");
            const role = document.getElementById("role");
            const username = document.getElementById("username");
            const birthdate = document.getElementById("birthdate");
            const age = document.getElementById("age");
            const email = document.getElementById("email");
            const saveBtn = document.getElementById("saveBtn");
            const form = document.getElementById("addUserForm");

            // Generate username
            function generateUsername() {
                let first = fname.value.trim().toLowerCase();
                let last = lname.value.trim().toLowerCase();
                let userRole = role.value.toLowerCase();
                username.value = (first && last && userRole) ? `${first}.${last}@${userRole}` : "";
                validateForm();
            }

            // Calculate age
            function calculateAge() {
                if (birthdate.value) {
                    let dob = new Date(birthdate.value);
                    let today = new Date();
                    let ageVal = today.getFullYear() - dob.getFullYear();
                    let m = today.getMonth() - dob.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                        ageVal--;
                    }
                    age.value = ageVal > 0 ? ageVal : 0;
                } else {
                    age.value = "";
                }
                validateForm();
            }

            // Validate form fields
            function validateForm() {
                let valid = fname.value && lname.value && age.value && birthdate.value &&
                    role.value && username.value && email.validity.valid;
                saveBtn.disabled = !valid;
            }

            fname.addEventListener("input", generateUsername);
            lname.addEventListener("input", generateUsername);
            role.addEventListener("change", generateUsername);
            birthdate.addEventListener("change", calculateAge);
            email.addEventListener("input", validateForm);

            // Submit form with AJAX
            form.addEventListener("submit", function(e) {
                e.preventDefault();
                const formData = new FormData(form);

                fetch("add_user.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        let toastEl = document.getElementById("toastMessage");
                        let toastBody = toastEl.querySelector(".toast-body");

                        if (data.status === "success") {
                            toastEl.classList.remove("bg-danger");
                            toastEl.classList.add("bg-success");
                            toastBody.textContent = "User added successfully!";

                            form.reset();
                            username.value = "";
                            age.value = "";
                            saveBtn.disabled = true;

                            // ✅ Hide modal
                            bootstrap.Modal.getInstance(document.getElementById("addUserModal")).hide();

                            setTimeout(() => location.reload(), 500);
                        } else {
                            toastEl.classList.remove("bg-success");
                            toastEl.classList.add("bg-danger");
                            toastBody.textContent = data.message;
                        }
                        new bootstrap.Toast(toastEl).show();
                    })
                    .catch(err => console.error(err));
            });
        });
    </script>


    <!-- Edit User Modal (same structure, fields prefilled via JS) -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="editUserForm" class="modal-content shadow-lg rounded-3 border-0">

                <!-- Header -->
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-2"></i> Edit User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Role</label>
                            <select name="role" id="edit-role" class="form-select" required>
                                <option value="admin">Admin</option>
                                <option value="encoder">Encoder</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" id="edit-username"
                                class="form-control" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">First Name</label>
                            <input type="text" name="fname" id="edit-fname"
                                class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Last Name</label>
                            <input type="text" name="lname" id="edit-lname"
                                class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Birthdate</label>
                            <input type="date" name="birthdate" id="edit-birthdate"
                                class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Age</label>
                            <input type="number" name="age" id="edit-age"
                                class="form-control" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Address</label>
                            <input type="text" name="address" id="edit-address"
                                class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="edit-email"
                                class="form-control" required>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" id="updateBtn" class="btn btn-warning fw-semibold">
                        <i class="bi bi-save me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- Toast / Popup for success -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
        <div id="editToastMessage" class="toast align-items-center text-white bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const editForm = document.getElementById("editUserForm");
            const fname = document.getElementById("edit-fname");
            const lname = document.getElementById("edit-lname");
            const role = document.getElementById("edit-role");
            const username = document.getElementById("edit-username");
            const birthdate = document.getElementById("edit-birthdate");
            const age = document.getElementById("edit-age");
            const email = document.getElementById("edit-email");

            // Generate username on role/fname/lname change
            function generateUsername() {
                let first = fname.value.trim().toLowerCase();
                let last = lname.value.trim().toLowerCase();
                let userRole = role.value.toLowerCase();
                username.value = (first && last && userRole) ? `${first}.${last}@${userRole}` : "";
            }

            // Calculate age from birthdate
            function calculateAge() {
                if (birthdate.value) {
                    let dob = new Date(birthdate.value);
                    let today = new Date();
                    let ageVal = today.getFullYear() - dob.getFullYear();
                    let m = today.getMonth() - dob.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                        ageVal--;
                    }
                    age.value = ageVal > 0 ? ageVal : 0;
                } else {
                    age.value = "";
                }
            }


            // Event listeners
            fname.addEventListener("input", generateUsername);
            lname.addEventListener("input", generateUsername);
            role.addEventListener("change", generateUsername);
            birthdate.addEventListener("change", calculateAge);


            // Handle submit via AJAX
            editForm.addEventListener("submit", function(e) {
                e.preventDefault();
                const formData = new FormData(editForm);

                fetch("update_user.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        let toastEl = document.getElementById("toastMessage"); // ✅ reuse same toast
                        let toastBody = toastEl.querySelector(".toast-body");

                        if (data.status === "success") {
                            toastEl.classList.remove("bg-danger");
                            toastEl.classList.add("bg-success");
                            toastBody.textContent = "User updated successfully!";

                            // ✅ Hide modal safely
                            let modalEl = document.getElementById("editUserModal");
                            let modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();

                            setTimeout(() => location.reload(), 500);
                        } else {
                            toastEl.classList.remove("bg-success");
                            toastEl.classList.add("bg-danger");
                            toastBody.textContent = data.message || "Update failed!";
                        }
                        new bootstrap.Toast(toastEl).show();
                    })
                    .catch(err => {
                        console.error(err);
                        let toastEl = document.getElementById("toastMessage");
                        let toastBody = toastEl.querySelector(".toast-body");
                        toastEl.classList.remove("bg-success");
                        toastEl.classList.add("bg-danger");
                        toastBody.textContent = "Server error. Please try again.";
                        new bootstrap.Toast(toastEl).show();
                    });
            });
        });
    </script>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="fs-5 mb-3">Are you sure you want to delete <strong id="deleteUserName"></strong>?</p>
                    <p class="text-muted small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger px-4">
                        <i class="bi bi-trash-fill me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var editModal = document.getElementById('editUserModal');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    document.getElementById('edit-id').value = button.getAttribute('data-id');
                    document.getElementById('edit-role').value = button.getAttribute('data-role');
                    document.getElementById('edit-username').value = button.getAttribute('data-username');
                    document.getElementById('edit-fname').value = button.getAttribute('data-fname');
                    document.getElementById('edit-lname').value = button.getAttribute('data-lname');
                    document.getElementById('edit-age').value = button.getAttribute('data-age');
                    document.getElementById('edit-birthdate').value = button.getAttribute('data-birthdate');
                    document.getElementById('edit-address').value = button.getAttribute('data-address');
                    document.getElementById('edit-email').value = button.getAttribute('data-email');
                });
            }

            // Delete confirmation modal
            let deleteUserId = null;
            const deleteUserNameEl = document.getElementById("deleteUserName");
            const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

            document.querySelectorAll('.deleteUserBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    deleteUserId = this.dataset.id;
                    let fname = this.dataset.fname || "";
                    let lname = this.dataset.lname || "";
                    deleteUserNameEl.textContent = fname + " " + lname;

                    let modal = new bootstrap.Modal(document.getElementById("deleteConfirmModal"));
                    modal.show();
                });
            });

            confirmDeleteBtn.addEventListener("click", function() {
                if (!deleteUserId) return;

                fetch("delete_user.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "id=" + deleteUserId
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === "success") {
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert("Error deleting user.");
                    });
            });
        });
    </script>


    <?php include 'footer.php'; ?>