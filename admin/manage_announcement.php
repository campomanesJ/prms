<?php
include 'header.php';
include 'db_connect.php';

if (!isset($_SESSION['login_id'])) {
    header("Location: ../index.php");
    exit;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_announcement') {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = "uploads/";
            $fileName = basename($_FILES["image"]["name"]);
            $targetFilePath = $targetDir . time() . "_" . $fileName;

            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $image_path = $targetFilePath;
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to upload image.']);
                exit;
            }
        }

        $sql = "INSERT INTO announcements (title, description, image_path) VALUES ('$title', '$description', " . ($image_path ? "'$image_path'" : "NULL") . ")";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => 'Announcement added successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
        }
        exit;
    } elseif ($_POST['action'] === 'update_announcement') {
        $id = intval($_POST['id']);
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        // Fetch existing image path
        $res = mysqli_query($conn, "SELECT image_path FROM announcements WHERE id = $id");
        $old = mysqli_fetch_assoc($res);
        $old_image = $old['image_path'];

        $image_path = $old_image;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = "uploads/";
            $fileName = basename($_FILES["image"]["name"]);
            $targetFilePath = $targetDir . time() . "_" . $fileName;

            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                // Delete old image if exists
                if ($old_image && file_exists($old_image)) {
                    unlink($old_image);
                }
                $image_path = $targetFilePath;
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to upload new image.']);
                exit;
            }
        }

        $sql = "UPDATE announcements SET title='$title', description='$description', image_path=" . ($image_path ? "'$image_path'" : "NULL") . " WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => 'Announcement updated successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
        }
        exit;
    } elseif ($_POST['action'] === 'delete_announcement') {
        $id = intval($_POST['id']);

        // Delete image file if exists
        $res = mysqli_query($conn, "SELECT image_path FROM announcements WHERE id = $id");
        $old = mysqli_fetch_assoc($res);
        if ($old && $old['image_path'] && file_exists($old['image_path'])) {
            unlink($old['image_path']);
        }

        $sql = "DELETE FROM announcements WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => 'Announcement deleted successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
        }
        exit;
    }
}

// Fetch all announcements for display
$result = mysqli_query($conn, "SELECT * FROM announcements ORDER BY id DESC");

?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" />

<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />

<style>
    /* Adjust for content-wrapper */
    .content-wrapper {
        background: #f4f6f9;
        padding: 20px 30px;
        min-height: calc(100vh - 56px);
        /* adjust based on your header height */
    }

    h2 {
        font-weight: 700;
        color: #343a40;
    }

    .swal2-input,
    .swal2-textarea {
        font-size: 1rem !important;
    }

    .dataTables_wrapper .dataTables_filter {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0.25rem;
        border: 1px solid #ced4da;
        padding: 0.375rem 0.75rem;
    }

    /* Container for Add button and search input aligned left and right */
    .dt-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        flex-wrap: wrap;
        gap: 10px;
    }

    /* Remove the card-header title */
    .card-header h3.card-title {
        display: none;
    }

    table.dataTable tbody tr:hover {
        background-color: #d2d6de !important;
        /* AdminLTE hover color */
    }

    img.announcement-img {
        max-width: 120px;
        max-height: 90px;
        border-radius: 6px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    /* Action buttons */
    .btn-action {
        margin: 0 2px;
    }

    /* Responsive for buttons */
    @media (max-width: 575.98px) {
        .dt-controls {
            flex-direction: column;
            align-items: stretch;
        }

        #btnAddAnnouncement {
            width: 100%;
        }
    }
</style>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include 'navbar.php'; ?>

        <div class="content-wrapper">
            <section class="content-header mb-3">
                <h2>Announcements</h2>
            </section>

            <section class="content">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <!-- Hide the announcement list title -->
                        <h3 class="card-title mb-0"></h3>
                    </div>
                    <div class="card-body">
                        <!-- Controls wrapper -->
                        <div class="dt-controls">
                            <button id="btnAddAnnouncement" class="btn btn-success">
                                <i class="fas fa-plus-circle"></i> Add Announcement
                            </button>
                            <!-- The search bar will be placed by DataTables -->
                        </div>
                        <table id="announcementsTable" class="table table-bordered table-hover dt-responsive nowrap" style="width:100%">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 5%;">ID</th>
                                    <th style="width: 25%;">Title</th>
                                    <th>Description</th>
                                    <th style="width: 20%;">Image</th>
                                    <th style="width: 15%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($announcement = mysqli_fetch_assoc($result)) : ?>
                                    <tr data-id="<?= $announcement['id'] ?>" data-title="<?= htmlspecialchars($announcement['title'], ENT_QUOTES) ?>" data-description="<?= htmlspecialchars($announcement['description'], ENT_QUOTES) ?>" data-image_path="<?= $announcement['image_path'] ?>">
                                        <td><?= $announcement['id'] ?></td>
                                        <td><?= htmlspecialchars($announcement['title']) ?></td>
                                        <td><?= nl2br(htmlspecialchars($announcement['description'])) ?></td>
                                        <td>
                                            <?php if ($announcement['image_path'] && file_exists($announcement['image_path'])) : ?>
                                                <img src="<?= $announcement['image_path'] ?>" alt="Image" class="announcement-img">
                                            <?php else : ?>
                                                <span class="text-muted">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm btn-action btnUpdate" title="Update"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-danger btn-sm btn-action btnDelete" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <?php include 'footer.php'; ?>
    </div>

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable with responsive support
            var table = $('#announcementsTable').DataTable({
                responsive: true,
                paging: true,
                pageLength: 10,
                lengthChange: false,
                info: false,
                searching: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search announcements..."
                },
                dom: "<'dt-controls'<'#btnAddWrapper'>f>" + // custom div for button and search
                    "rt" +
                    "ip"
            });

            // Move Add Announcement button into custom div
            $('#btnAddAnnouncement').appendTo('#btnAddWrapper');

            // Add Announcement
            $('#btnAddAnnouncement').on('click', function() {
                Swal.fire({
                    title: 'Add Announcement',
                    html: `<input type="text" id="swal-input1" class="swal2-input" placeholder="Title" required>` +
                        `<textarea id="swal-input2" class="swal2-textarea" placeholder="Description (optional)"></textarea>` +
                        `<input type="file" id="swal-input3" class="swal2-file" accept="image/*" style="margin-top:10px;">`,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    preConfirm: () => {
                        const title = Swal.getPopup().querySelector('#swal-input1').value.trim();
                        const description = Swal.getPopup().querySelector('#swal-input2').value.trim();
                        const imageFile = Swal.getPopup().querySelector('#swal-input3').files[0];

                        if (!title) {
                            Swal.showValidationMessage('Title is required');
                            return false;
                        }

                        return {
                            title: title,
                            description: description,
                            imageFile: imageFile
                        };
                    },
                    didOpen: () => {
                        Swal.getPopup().querySelector('#swal-input1').focus();
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('action', 'add_announcement');
                        formData.append('title', result.value.title);
                        formData.append('description', result.value.description);
                        if (result.value.imageFile) {
                            formData.append('image', result.value.imageFile);
                        }

                        Swal.fire({
                            title: 'Adding announcement...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: '',
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                Swal.close();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Announcement added successfully.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.close();
                                let errMsg = 'An error occurred.';
                                try {
                                    let res = JSON.parse(xhr.responseText);
                                    if (res.error) errMsg = res.error;
                                } catch {}
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: errMsg
                                });
                            }
                        });
                    }
                });
            });

            // Update Announcement
            $('#announcementsTable').on('click', '.btnUpdate', function() {
                const tr = $(this).closest('tr');
                const id = tr.data('id');
                const title = tr.data('title');
                const description = tr.data('description');
                const image_path = tr.data('image_path');

                Swal.fire({
                    title: 'Update Announcement',
                    html: `<input type="text" id="swal-input1" class="swal2-input" placeholder="Title" required value="${title}">` +
                        `<textarea id="swal-input2" class="swal2-textarea" placeholder="Description (optional)">${description}</textarea>` +
                        `<div style="margin-top:10px;">
                            <label>Current Image:</label><br>` +
                        (image_path ? `<img src="${image_path}" alt="Current Image" style="max-width:100px; max-height:80px; border-radius:6px; margin-bottom:10px;">` : '<span class="text-muted">No Image</span>') +
                        `</div>` +
                        `<input type="file" id="swal-input3" class="swal2-file" accept="image/*" style="margin-top:10px;">`,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    preConfirm: () => {
                        const newTitle = Swal.getPopup().querySelector('#swal-input1').value.trim();
                        const newDesc = Swal.getPopup().querySelector('#swal-input2').value.trim();
                        const imageFile = Swal.getPopup().querySelector('#swal-input3').files[0];

                        if (!newTitle) {
                            Swal.showValidationMessage('Title is required');
                            return false;
                        }

                        return {
                            id: id,
                            title: newTitle,
                            description: newDesc,
                            imageFile: imageFile
                        };
                    },
                    didOpen: () => {
                        Swal.getPopup().querySelector('#swal-input1').focus();
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('action', 'update_announcement');
                        formData.append('id', result.value.id);
                        formData.append('title', result.value.title);
                        formData.append('description', result.value.description);
                        if (result.value.imageFile) {
                            formData.append('image', result.value.imageFile);
                        }

                        Swal.fire({
                            title: 'Updating announcement...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: '',
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                Swal.close();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Announcement updated successfully.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.close();
                                let errMsg = 'An error occurred.';
                                try {
                                    let res = JSON.parse(xhr.responseText);
                                    if (res.error) errMsg = res.error;
                                } catch {}
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: errMsg
                                });
                            }
                        });
                    }
                });
            });

            // Delete Announcement
            $('#announcementsTable').on('click', '.btnDelete', function() {
                const tr = $(this).closest('tr');
                const id = tr.data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will delete the announcement permanently.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '',
                            method: 'POST',
                            data: {
                                action: 'delete_announcement',
                                id: id
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Announcement has been deleted.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                let errMsg = 'An error occurred.';
                                try {
                                    let res = JSON.parse(xhr.responseText);
                                    if (res.error) errMsg = res.error;
                                } catch {}
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: errMsg
                                });
                            }
                        });
                    }
                });
            });

        });
    </script>
</body>