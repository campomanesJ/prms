<?php
include 'header.php';
include 'db_connect.php';
if (!isset($_SESSION['login_id'])) {
    header("Location: ../index.php");
    exit;
}

?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" />
<!-- DataTables Responsive CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" />

<style>
    .bg-primary {
        background-color: rgb(240, 240, 240) !important;
    }

    .table .thead-dark th {
        color: #fff;
        background-color: rgba(204, 204, 204, 0.77);
        border-color: rgb(212, 212, 212);
    }

    thead {
        background-color: rgba(135, 206, 235, 0.5) !important;
    }
</style>

<body class="hold-transition layout-fixed">
<div class="wrapper">
    <?php include 'navbar.php'; ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 mx-auto mt-4">
                    <div class="card shadow-lg">
                        <div class="card-header bg-primary text-white">
                            <h5 style="color: black; font-size: 40px; font-weight: bold;" class="mb-0 d-inline">Users List</h5>
                            <button id="addUserBtn" class="btn btn-success btn-sm float-right">+ Add User</button>
                        </div>
                        <div class="card-body">
                            <table id="usersTable" class="table table-bordered table-hover dt-responsive nowrap" style="width:100%">
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
<?php
$sql = "SELECT * FROM user";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . ucfirst(htmlspecialchars($row['role'])) . "</td>
                <td>" . htmlspecialchars($row['username']) . "</td>
                <td>" . htmlspecialchars($row['fname']) . " " . htmlspecialchars($row['lname']) . "</td>
                <td>" . htmlspecialchars($row['age']) . "</td>
                <td>" . htmlspecialchars($row['birthdate']) . "</td>
                <td>" . htmlspecialchars($row['address']) . "</td>
                <td>" . htmlspecialchars($row['email']) . "</td>
                <td>
                    <button class='btn btn-warning btn-sm editUserBtn'
                        data-id='{$row['id']}'
                        data-username='" . htmlspecialchars($row['username']) . "'
                        data-fname='" . htmlspecialchars($row['fname']) . "'
                        data-lname='" . htmlspecialchars($row['lname']) . "'
                        data-age='" . htmlspecialchars($row['age']) . "'
                        data-birthdate='" . htmlspecialchars($row['birthdate']) . "'
                        data-address='" . htmlspecialchars($row['address']) . "'
                        data-email='" . htmlspecialchars($row['email']) . "'
                        data-role='" . htmlspecialchars($row['role']) . "'>Edit</button>";
        
        if (strtolower($row['role']) !== 'admin') {
            echo " <button class='btn btn-danger btn-sm deleteUserBtn' data-id='{$row['id']}'>Delete</button>";
        }

        echo "</td></tr>";
    }
} else {
    echo "<tr><td colspan='8' class='text-center'>No users found</td></tr>";
}
$conn->close();
?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        responsive: true,
        order: []
    });
    $('#addUserBtn').on('click', function() {
        Swal.fire({
            title: 'Add New User',
            html: `
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <select id="swal-role" class="swal2-input">
                        <option value="admin">Admin</option>
                        <option value="encoder">Encoder</option>
                    </select>
                    <input type="text" id="swal-fname" class="swal2-input" placeholder="First Name">
                    <input type="text" id="swal-lname" class="swal2-input" placeholder="Last Name">
                    <input type="text" id="swal-username" class="swal2-input" placeholder="Generated Username" disabled>
                    <input type="number" id="swal-age" class="swal2-input" placeholder="Age">
                    <input type="date" id="swal-birthdate" class="swal2-input">
                    <input type="text" id="swal-address" class="swal2-input" placeholder="Address">
                    <input type="email" id="swal-email" class="swal2-input" placeholder="Email">
                </div>
            `,
            confirmButtonText: 'Add User',
            showCancelButton: true,
            didOpen: () => {
                const fnameInput = document.getElementById("swal-fname");
                const lnameInput = document.getElementById("swal-lname");
                const roleSelect = document.getElementById("swal-role");
                const usernameInput = document.getElementById("swal-username");

                function generateUsername() {
                    const fname = fnameInput.value.toLowerCase().replace(/\s+/g, '');
                    const lname = lnameInput.value.toLowerCase().replace(/\s+/g, '');
                    const role = roleSelect.value.toLowerCase();
                    if (fname && lname && role) {
                        usernameInput.value = `${fname}.${lname}@${role}`;
                    } else {
                        usernameInput.value = '';
                    }
                }

                fnameInput.addEventListener("input", generateUsername);
                lnameInput.addEventListener("input", generateUsername);
                roleSelect.addEventListener("change", generateUsername);
            },
            preConfirm: () => {
                const role = $('#swal-role').val().toLowerCase();
                const fname = $('#swal-fname').val().trim();
                const lname = $('#swal-lname').val().trim();
                const username = `${fname.toLowerCase().replace(/\s+/g, '')}.${lname.toLowerCase().replace(/\s+/g, '')}@${role}`;
                const age = $('#swal-age').val().trim();
                const birthdate = $('#swal-birthdate').val().trim();
                const address = $('#swal-address').val().trim();
                const email = $('#swal-email').val().trim();
                const password = 'prms@matalom'; // Default password

                if (!role || !fname || !lname || !age || !birthdate || !address || !email) {
                    Swal.showValidationMessage('❌ All fields are required!');
                    return false;
                }

                return fetch('add_user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        role, username, fname, lname, age, birthdate, address, email, password
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('✅ User Added!', '', 'success').then(() => location.reload());
                    } else {
                        Swal.fire('❌ Error!', data.message, 'error');
                    }
                });
            }
        });
    });
    $('#usersTable tbody').on('click', '.editUserBtn', function() {
        const btn = $(this);
        const userId = btn.data('id');
        const role = btn.data('role');
        const username = btn.data('username');
        const fname = btn.data('fname');
        const lname = btn.data('lname');
        const age = btn.data('age');
        const birthdate = btn.data('birthdate');
        const address = btn.data('address');
        const email = btn.data('email');

        Swal.fire({
            title: 'Edit User',
            html: `
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <select id="swal-role" class="swal2-input">
                        <option value="admin" ${role === 'admin' ? 'selected' : ''}>Admin</option>
                        <option value="encoder" ${role === 'encoder' ? 'selected' : ''}>Encoder</option>
                    </select>
                    <input type="text" id="swal-username" class="swal2-input" value="${username}" disabled>
                    <input type="text" id="swal-fname" class="swal2-input" value="${fname}">
                    <input type="text" id="swal-lname" class="swal2-input" value="${lname}">
                    <input type="number" id="swal-age" class="swal2-input" value="${age}">
                    <input type="date" id="swal-birthdate" class="swal2-input" value="${birthdate}">
                    <input type="text" id="swal-address" class="swal2-input" value="${address}">
                    <input type="email" id="swal-email" class="swal2-input" value="${email}">
                </div>
            `,
            confirmButtonText: 'Update User',
            showCancelButton: true,
            preConfirm: () => {
                const formData = new URLSearchParams({
                    id: userId,
                    role: $('#swal-role').val().toLowerCase(),
                    username: username,
                    fname: $('#swal-fname').val().trim(),
                    lname: $('#swal-lname').val().trim(),
                    age: $('#swal-age').val().trim(),
                    birthdate: $('#swal-birthdate').val().trim(),
                    address: $('#swal-address').val().trim(),
                    email: $('#swal-email').val().trim(),
                });

                return fetch('update_user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('✅ User Updated!', '', 'success').then(() => location.reload());
                    } else {
                        Swal.fire('❌ Error!', data.message, 'error');
                    }
                });
            }
        });
    });

    $('#usersTable tbody').on('click', '.deleteUserBtn', function() {
        const userId = $(this).data('id');
        Swal.fire({
            title: "Are you sure?",
            text: "This action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then(result => {
            if (result.isConfirmed) {
                fetch("delete_user.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "id=" + userId
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        Swal.fire("Deleted!", "User has been removed.", "success").then(() => location.reload());
                    } else {
                        Swal.fire("Error!", data.message, "error");
                    }
                });
            }
        });
    });
});
</script>

</body>
<?php include 'footer.php'; ?>
