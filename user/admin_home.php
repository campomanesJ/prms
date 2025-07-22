<?php
session_start();
if (!isset($_SESSION['login_userid']) || $_SESSION['login_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<?php include 'components/navbar.php'; ?>

<div class="container mt-5">
    <h1 class="text-center">Welcome, Admin <?= htmlspecialchars($_SESSION['login_username']) ?></h1>
    <p class="text-center">You have administrative access.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
