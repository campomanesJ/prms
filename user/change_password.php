<?php
session_start();
include 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['login_id'])) {
    header("Location: ../index.php");
    exit;
}

$success = $error = "";

// 🟢 Handle password change
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $id = $_SESSION['login_id'];

    // Fetch user
    $stmt = $conn->prepare("SELECT password FROM user WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    // Regex: min 8 chars, 1 uppercase, 1 lowercase, 1 digit, 1 special char
    $password_pattern = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/";

    // Validate
    if (!password_verify($current_password, $hashed_password)) {
        $error = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New password and confirmation do not match.";
    } elseif (!preg_match($password_pattern, $new_password)) {
        $error = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
    } else {
        $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE user SET password = ? WHERE id = ?");
        $update->bind_param("si", $new_hashed, $id);
        if ($update->execute()) {
            $success = "Password updated successfully.";
        } else {
            $error = "Error updating password.";
        }
        $update->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .input-group .btn.toggle-password {
            border-color: #ced4da;
        }
    </style>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <?php include 'navbar.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper p-4">
            <section class="content">
                <div class="container-fluid">

                    <div class="row justify-content-center">
                        <div class="col-md-6">

                            <div class="card shadow-lg border-0">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-key"></i> Change Password</h5>
                                </div>
                                <div class="card-body">

                                    <?php if ($success): ?>
                                        <div class="alert alert-success"><?= $success ?></div>
                                    <?php endif; ?>
                                    <?php if ($error): ?>
                                        <div class="alert alert-danger"><?= $error ?></div>
                                    <?php endif; ?>

                                    <form method="POST" action="">
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Current Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="current_password" aria-label="Show/Hide current password">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="new_password" class="form-label">New Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="new_password" aria-label="Show/Hide new password">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </div>
                                            <small id="passwordHelp" class="form-text text-muted">
                                                Must be at least 8 characters, include uppercase, lowercase, number, and special character.
                                            </small>
                                            <ul class="mt-2" id="password-checklist" style="list-style:none; padding-left:0;">
                                                <li id="length" class="text-danger">❌ At least 8 characters</li>
                                                <li id="uppercase" class="text-danger">❌ At least one uppercase letter</li>
                                                <li id="lowercase" class="text-danger">❌ At least one lowercase letter</li>
                                                <li id="number" class="text-danger">❌ At least one number</li>
                                                <li id="special" class="text-danger">❌ At least one special character</li>
                                            </ul>
                                        </div>
                                        <!-- Confirm Password -->
                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label">Confirm Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="confirm_password" aria-label="Show/Hide confirm password">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Update Password</button>
                                    </form>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </section>
        </div>

    </div>

    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>

    <script>
        document.querySelectorAll('.toggle-password').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = document.getElementById(this.dataset.target);
                const icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    this.setAttribute('aria-label', 'Hide password');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    this.setAttribute('aria-label', 'Show password');
                }
            });
        });

        // 🔐 Password live checker
        const newPassword = document.getElementById('new_password');
        const checklist = {
            length: document.getElementById('length'),
            uppercase: document.getElementById('uppercase'),
            lowercase: document.getElementById('lowercase'),
            number: document.getElementById('number'),
            special: document.getElementById('special'),
        };

        newPassword.addEventListener('input', () => {
            const value = newPassword.value;
            checklist.length.className = value.length >= 8 ? "text-success" : "text-danger";
            checklist.length.textContent = value.length >= 8 ? "✅ At least 8 characters" : "❌ At least 8 characters";

            checklist.uppercase.className = /[A-Z]/.test(value) ? "text-success" : "text-danger";
            checklist.uppercase.textContent = /[A-Z]/.test(value) ? "✅ At least one uppercase letter" : "❌ At least one uppercase letter";

            checklist.lowercase.className = /[a-z]/.test(value) ? "text-success" : "text-danger";
            checklist.lowercase.textContent = /[a-z]/.test(value) ? "✅ At least one lowercase letter" : "❌ At least one lowercase letter";

            checklist.number.className = /\d/.test(value) ? "text-success" : "text-danger";
            checklist.number.textContent = /\d/.test(value) ? "✅ At least one number" : "❌ At least one number";

            checklist.special.className = /[^A-Za-z0-9]/.test(value) ? "text-success" : "text-danger";
            checklist.special.textContent = /[^A-Za-z0-9]/.test(value) ? "✅ At least one special character" : "❌ At least one special character";
        });
    </script>
</body>

</html>