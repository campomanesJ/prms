<?php
session_start();
include 'header.php';
include 'db_connect.php';

if (!isset($_SESSION['login_id'])) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Parish Staff List</title>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" />
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
</head>
<body class="hold-transition layout-fixed">
<div class="wrapper">
    <?php include 'navbar.php'; ?>

    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 mx-auto mt-4">
                    <div class="card shadow-lg">
                        <div class="card-header bg-primary text-white">
                            <h5 style="color: black; font-size: 40px; font-weight: bold;" class="mb-0 d-inline">Parish Staff List</h5>
                            <button id="addUserBtn" class="btn btn-success btn-sm float-right">+ Add Staff</button>
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
$sql = "SELECT id, role, username, fname, mname, lname, birthdate, address, email FROM parish_staff";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $role = strtolower($row['role']);
        $displayRole = ucfirst($role);
        if ($role === 'priest') $displayRole = 'Parish Priest';
        if ($role === 'registrar') $displayRole = 'Parish Registrar';

        $fullName = htmlspecialchars($row['fname'], ENT_QUOTES);
if (!empty($row['mname'])) {
    $fullName .= ' ' . htmlspecialchars($row['mname'], ENT_QUOTES);
}
$fullName .= ' ' . htmlspecialchars($row['lname'], ENT_QUOTES);

// Add "Fr." prefix if role is priest
if ($role === 'priest') {
    $fullName = 'Fr. ' . $fullName;
}


        $age = '';
        if (!empty($row['birthdate'])) {
            $birthDate = new DateTime($row['birthdate']);
            $today = new DateTime('today');
            $age = $birthDate->diff($today)->y;
        }

        echo '<tr>';
        echo '<td>' . $displayRole . '</td>';
        echo '<td>' . htmlspecialchars($row['username'], ENT_QUOTES) . '</td>';
        echo '<td>' . $fullName . '</td>';
        echo '<td>' . htmlspecialchars($age, ENT_QUOTES) . '</td>';
        echo '<td>' . htmlspecialchars($row['birthdate'], ENT_QUOTES) . '</td>';
        echo '<td>' . htmlspecialchars($row['address'], ENT_QUOTES) . '</td>';
        echo '<td>' . htmlspecialchars($row['email'], ENT_QUOTES) . '</td>';
        echo '<td>';
        echo '<button class="btn btn-warning btn-sm editUserBtn" ' .
             'data-id="' . $row['id'] . '" ' .
             'data-role="' . htmlspecialchars($row['role'], ENT_QUOTES) . '" ' .
             'data-username="' . htmlspecialchars($row['username'], ENT_QUOTES) . '" ' .
             'data-fname="' . htmlspecialchars($row['fname'], ENT_QUOTES) . '" ' .
             'data-mname="' . htmlspecialchars($row['mname'], ENT_QUOTES) . '" ' .
             'data-lname="' . htmlspecialchars($row['lname'], ENT_QUOTES) . '" ' .
             'data-birthdate="' . htmlspecialchars($row['birthdate'], ENT_QUOTES) . '" ' .
             'data-address="' . htmlspecialchars($row['address'], ENT_QUOTES) . '" ' .
             'data-email="' . htmlspecialchars($row['email'], ENT_QUOTES) . '"' .
             '>Edit</button> ';
        echo '<button class="btn btn-danger btn-sm deleteUserBtn" data-id="' . $row['id'] . '">Delete</button>';
        echo '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="8" class="text-center">No staff found</td></tr>';
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

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#usersTable').DataTable({ responsive: true, order: [] });

    function generateUsername() {
        let role = $('#swal-role').val();
        let fname = $('#swal-fname').val().toLowerCase().replace(/\s+/g, '');
        let lname = $('#swal-lname').val().toLowerCase().replace(/\s+/g, '');
        if (role && fname && lname) {
            $('#swal-username').val(`${fname}.${lname}@${role}`);
        } else {
            $('#swal-username').val('');
        }
    }

    function calculateAge(birthdateStr) {
        const birthdate = new Date(birthdateStr);
        const today = new Date();
        let age = today.getFullYear() - birthdate.getFullYear();
        const m = today.getMonth() - birthdate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthdate.getDate())) {
            age--;
        }
        return isNaN(age) ? '' : age;
    }

    function bindAgeField() {
        $('#swal-birthdate').on('change', function() {
            const birthdate = $(this).val();
            const age = calculateAge(birthdate);
            $('#swal-age').val(age);
        }).trigger('change');
    }

    $('#addUserBtn').on('click', function() {
        Swal.fire({
            title: 'Add New Staff',
            html: `
                <select id="swal-role" class="swal2-input">
                    <option value="" disabled selected>Select Role</option>
                    <option value="priest">Parish Priest</option>
                    <option value="registrar">Parish Registrar</option>
                </select>
                <input type="text" id="swal-fname" class="swal2-input" placeholder="First Name" />
                <input type="text" id="swal-mname" class="swal2-input" placeholder="Middle Name (optional)" />
                <input type="text" id="swal-lname" class="swal2-input" placeholder="Last Name" />
                <input type="text" id="swal-username" class="swal2-input" placeholder="Username" disabled />
                <input type="date" id="swal-birthdate" class="swal2-input" />
                <input type="text" id="swal-age" class="swal2-input" placeholder="Age" disabled />
                <input type="text" id="swal-address" class="swal2-input" placeholder="Address" />
                <input type="email" id="swal-email" class="swal2-input" placeholder="Email" />
            `,
            focusConfirm: false,
            preConfirm: () => {
                const role = $('#swal-role').val();
                const fname = $('#swal-fname').val().trim();
                const mname = $('#swal-mname').val().trim();
                const lname = $('#swal-lname').val().trim();
                const username = $('#swal-username').val().trim();
                const birthdate = $('#swal-birthdate').val().trim();
                const address = $('#swal-address').val().trim();
                const email = $('#swal-email').val().trim();

                if (!role || !fname || !lname || !username || !birthdate || !address || !email) {
                    Swal.showValidationMessage('Please fill in all required fields');
                    return false;
                }

                return {role, fname, mname, lname, username, birthdate, address, email};
            },
            didOpen: () => {
                $('#swal-role, #swal-fname, #swal-lname').on('input change', generateUsername);
                bindAgeField();
            },
            confirmButtonText: 'Add Staff',
            showCancelButton: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('add_staff.php', result.value, function(response) {
                    try {
                        const res = JSON.parse(response);
                        if (res.status === 'success') {
                            Swal.fire('Added!', 'Staff has been added.', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', res.message || 'Failed to add staff.', 'error');
                        }
                    } catch {
                        Swal.fire('Error', 'Unexpected server response.', 'error');
                    }
                });
            }
        });
    });

    $('#usersTable').on('click', '.editUserBtn', function() {
        const btn = $(this);
        Swal.fire({
            title: 'Edit Staff',
            html: `
                <select id="swal-role" class="swal2-input">
                    <option value="priest">Parish Priest</option>
                    <option value="registrar">Parish Registrar</option>
                </select>
                <input type="text" id="swal-fname" class="swal2-input" placeholder="First Name" />
                <input type="text" id="swal-mname" class="swal2-input" placeholder="Middle Name (optional)" />
                <input type="text" id="swal-lname" class="swal2-input" placeholder="Last Name" />
                <input type="text" id="swal-username" class="swal2-input" placeholder="Username" disabled />
                <input type="date" id="swal-birthdate" class="swal2-input" />
                <input type="text" id="swal-age" class="swal2-input" placeholder="Age" disabled />
                <input type="text" id="swal-address" class="swal2-input" placeholder="Address" />
                <input type="email" id="swal-email" class="swal2-input" placeholder="Email" />
            `,
            focusConfirm: false,
            didOpen: () => {
                $('#swal-role').val(btn.data('role'));
                $('#swal-fname').val(btn.data('fname'));
                $('#swal-mname').val(btn.data('mname'));
                $('#swal-lname').val(btn.data('lname'));
                $('#swal-username').val(btn.data('username'));
                $('#swal-birthdate').val(btn.data('birthdate'));
                $('#swal-address').val(btn.data('address'));
                $('#swal-email').val(btn.data('email'));
                $('#swal-role, #swal-fname, #swal-lname').on('input change', generateUsername);
                bindAgeField();
            },
            preConfirm: () => {
                const role = $('#swal-role').val();
                const fname = $('#swal-fname').val().trim();
                const mname = $('#swal-mname').val().trim();
                const lname = $('#swal-lname').val().trim();
                const username = $('#swal-username').val().trim();
                const birthdate = $('#swal-birthdate').val().trim();
                const address = $('#swal-address').val().trim();
                const email = $('#swal-email').val().trim();

                if (!role || !fname || !lname || !username || !birthdate || !address || !email) {
                    Swal.showValidationMessage('Please fill in all required fields');
                    return false;
                }

                return {id: btn.data('id'), role, fname, mname, lname, username, birthdate, address, email};
            },
            confirmButtonText: 'Update Staff',
            showCancelButton: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('update_staff.php', result.value, function(response) {
                    try {
                        const res = JSON.parse(response);
                        if (res.status === 'success') {
                            Swal.fire('Updated!', 'Staff details have been updated.', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', res.message || 'Failed to update staff.', 'error');
                        }
                    } catch {
                        Swal.fire('Error', 'Unexpected server response.', 'error');
                    }
                });
            }
        });
    });

    $('#usersTable').on('click', '.deleteUserBtn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('delete_staff.php', {id: id}, function(response) {
                    try {
                        const res = JSON.parse(response);
                        if (res.status === 'success') {
                            Swal.fire('Deleted!', 'Staff has been deleted.', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', res.message || 'Failed to delete staff.', 'error');
                        }
                    } catch {
                        Swal.fire('Error', 'Unexpected server response.', 'error');
                    }
                });
            }
        });
    });
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>
