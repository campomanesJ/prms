<?php
include "function.php";
include "db_connect.php";

if (isset($_SESSION['login_userid'])) {
    header('location: ../admin/home.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Page</title>

    <script src="../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="../assets/dist/js/adminlte.min.js"></script>
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            position: relative;
            background-image: url('../assets/img/loginp.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        body::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.3);
            z-index: -1;
        }

        .card {
            background: rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(4px);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .left-form {
            background: linear-gradient(rgb(0, 0, 0), rgb(2, 97, 2));
        }
    </style>
</head>

<body>
    <div class="d-flex flex-column min-vh-100">
        <div class="container d-flex justify-content-center align-items-center flex-grow-1">
            <div class="card w-100 shadow">
                <div class="row">
                    <div class="col-md-6 d-none d-md-flex justify-content-center p-3 left-form">
                        <div class="container">
                            <div id="carouselExample" class="carousel slide w-100" data-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="../assets/img/loginlogo.png" class="d-block w-100" alt="Slide" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 p-3">
                        <div class="container p-4">
                            <div class="mb-3">
                                <a href="../index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>

                            <h1 class="mb-4 text-center" style="font-weight:bold; font-size: 48px;">
                                Sign In
                            </h1>
                            <form action="#" method="post">
                                <div class="form-group mb-4">
                                    <label for="username">Username</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="username"
                                        id="username"
                                        placeholder="Enter your username"
                                        autofocus
                                        autocomplete="on" />
                                </div>
                                <div class="form-group mb-4">
                                    <label for="password">Password</label>
                                    <div class="input-group">
                                        <input
                                            type="password"
                                            class="form-control"
                                            name="password"
                                            id="password"
                                            placeholder="Enter your password"
                                            autocomplete="on" />
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary w-50" name="btn-login">
                                        Login
                                    </button>
                                </div>
                                <hr />
                                <div class="login-footer mt-3 text-center">
                                    <a href="forgot_password.php" style="text-decoration: underline; margin-right: 20px;">Forgot Password?</a>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="mt-auto text-white py-4">
            <div class="container text-center">
                <p class="mb-2">PRMS-Matalom. All Rights Reserved &copy; <?php echo date("Y"); ?></p>
                <p class="mb-2">
                    By
                    <a
                        href="https://web.facebook.com/superjansnoww"
                        target="_blank"
                        rel="noopener noreferrer"
                        style="text-decoration: underline; color: white;">BULALA BOYZ</a>
                </p>
            </div>
        </footer>
    </div>

</body>
<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>

</html>