<?php

include "function.php";

if (isset($_SESSION['login_userid'])) {
    header('location: ../admin/home.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="../assets/dist/js/adminlte.min.js"></script>
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
</head>

<style>
    body {
        position: relative;
        background-image: url('../assets/img/loginp.jpg');
        /* replace with your preferred background */
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
        filter: blur(5px);
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
        -webkit-backdrop-filter: blur(4px);
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    .left-form {
        background: linear-gradient(rgb(0, 0, 0), rgb(2, 97, 2));
    }
</style>


<body>
    <!-- <video autoplay muted loop class="video-background">
        <source src="../assets/img/background video.mp4" type="video/mp4">
    </video> -->

    <div class="d-flex flex-column min-vh-100">
        <div class="container d-flex justify-content-center align-items-center flex-grow-1">
            <div class="card w-100 shadow">
                <div class="row">
                    <div class="col-md-6 d-none d-md-flex justify-content-center p-3 left-form">
                        <div class="container">
                            <div id="carouselExample" class="carousel slide w-100" data-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="../assets/img/loginlogo.png" class="d-block w-100" alt="First slide">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="../assets/img/loginlogo.png" class="d-block w-100" alt="Second slide">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="../assets/img/loginlogo.png" class="d-block w-100" alt="Third slide">
                                    </div>
                                </div>
                                <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 p-3">
                        <div class="container p-4">
                            <h1 class="mb-4 text-center" style="font-weight:bold; font-size: 48px;">Password Recovery</h1>
                            <form id="otpForm">
                                <div class="form-group mb-4">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
                                </div>
                                <div class="text-center">
                                    <button type="button" class="btn btn-primary w-50" id="sendOtpBtn">Send OTP</button>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="login.php" class="text-primary" style="text-decoration: underline;">Back to Login</a>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#sendOtpBtn').click(function() {
                var email = $('#email').val().trim();
                if (email === '') {
                    Swal.fire('Error', 'Please enter your email', 'error');
                    return;
                }
                Swal.fire({
                    title: 'Sending OTP...',
                    text: 'Please wait a moment',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: 'send_otp.php',
                    type: 'POST',
                    data: {
                        email: email
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Enter OTP',
                                input: 'text',
                                inputLabel: 'Check your email for the OTP',
                                inputPlaceholder: 'Enter OTP here',
                                showCancelButton: true,
                                confirmButtonText: 'Verify OTP',
                                preConfirm: (otp) => {
                                    return $.ajax({
                                        url: 'verify_otp.php',
                                        type: 'POST',
                                        data: {
                                            otp: otp
                                        },
                                        dataType: 'json'
                                    }).then(response => {
                                        if (response.status === 'success') {
                                            return Swal.fire({
                                                title: 'OTP Verified!',
                                                html: `
                                            <label for="new_password">Enter New Password:</label>
                                            <input type="password" id="new_password" class="swal2-input" placeholder="New Password">
                                            <div id="password-requirements" style="margin-top: 10px; text-align: center; font-size:10px">
                                                <p style="color:red;">❌ Password must be at least 8 characters</p>
                                                <p style="color:red;">❌ Password must contain an uppercase letter</p>
                                                <p style="color:red;">❌ Password must contain a lowercase letter</p>
                                                <p style="color:red;">❌ Password must contain a number</p>
                                                <p style="color:red;">❌ Password must contain a special character</p>
                                            </div>
                                            <label for="confirm_password">Confirm Password:</label>
                                            <input type="password" id="confirm_password" class="swal2-input" placeholder="Confirm Password">
                                        `,
                                                showCancelButton: true,
                                                confirmButtonText: 'Change Password',
                                                didOpen: () => {
                                                    let passwordInput = $("#new_password");
                                                    let confirmPasswordInput = $("#confirm_password");
                                                    let changePasswordBtn = $(".swal2-confirm");
                                                    let passwordRequirements = $("#password-requirements");
                                                    let rules = [{
                                                            regex: /.{8,}/,
                                                            text: "Password must be at least 8 characters"
                                                        },
                                                        {
                                                            regex: /[A-Z]/,
                                                            text: "Password must contain an uppercase letter"
                                                        },
                                                        {
                                                            regex: /[a-z]/,
                                                            text: "Password must contain a lowercase letter"
                                                        },
                                                        {
                                                            regex: /[0-9]/,
                                                            text: "Password must contain a number"
                                                        },
                                                        {
                                                            regex: /[\W]/,
                                                            text: "Password must contain a special character"
                                                        }
                                                    ];

                                                    function checkPasswordStrength() {
                                                        let password = passwordInput.val();
                                                        let confirmPassword = confirmPasswordInput.val();
                                                        let allMet = true;
                                                        let requirementsHtml = "";
                                                        rules.forEach(rule => {
                                                            if (rule.regex.test(password)) {
                                                                requirementsHtml += `<p style="color:green;">✅ ${rule.text}</p>`;
                                                            } else {
                                                                requirementsHtml += `<p style="color:red;">❌ ${rule.text}</p>`;
                                                                allMet = false;
                                                            }
                                                        });
                                                        if (allMet) {
                                                            passwordRequirements.html('<p style="color:green; font-weight:bold;">✅ Password meets all the requirements</p>');
                                                        } else {
                                                            passwordRequirements.html(requirementsHtml);
                                                        }
                                                        if (allMet && password === confirmPassword && password !== "") {
                                                            changePasswordBtn.prop("disabled", false);
                                                        } else {
                                                            changePasswordBtn.prop("disabled", true);
                                                        }
                                                        if (confirmPassword !== "" && password !== confirmPassword) {
                                                            passwordRequirements.append('<p style="color:red;">❌ Passwords do not match</p>');
                                                            changePasswordBtn.prop("disabled", true);
                                                        }
                                                    }
                                                    passwordInput.on("input", checkPasswordStrength);
                                                    confirmPasswordInput.on("input", checkPasswordStrength);
                                                },
                                                preConfirm: () => {
                                                    var newPassword = $('#new_password').val();
                                                    var confirmPassword = $('#confirm_password').val();
                                                    if (newPassword === '' || confirmPassword === '') {
                                                        Swal.showValidationMessage('Please fill in both password fields');
                                                        return false;
                                                    }
                                                    if (newPassword !== confirmPassword) {
                                                        Swal.showValidationMessage('Passwords do not match');
                                                        return false;
                                                    }
                                                    return $.ajax({
                                                        url: 'change_password.php',
                                                        type: 'POST',
                                                        data: {
                                                            password: newPassword
                                                        },
                                                        dataType: 'json'
                                                    }).then(response => {
                                                        if (response.status === 'success') {
                                                            Swal.fire('Success', 'Your password has been changed.', 'success')
                                                                .then(() => {
                                                                    window.location.href = 'login.php';
                                                                });
                                                        } else {
                                                            Swal.fire('Error', response.message, 'error');
                                                        }
                                                    }).catch(() => {
                                                        Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                                                    });
                                                }
                                            });
                                        } else {
                                            Swal.fire('Error', response.message, 'error');
                                        }
                                    }).catch(() => {
                                        Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                                    });
                                }
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire('Error', 'Failed to send OTP. Try again later.', 'error');
                    }
                });
            });
        });
    </script>
</body>

</html>