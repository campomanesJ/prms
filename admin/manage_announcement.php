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

$result = mysqli_query($conn, "SELECT * FROM announcements ORDER BY id DESC");

// 🔹 Pagination + Search setup
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10; // default 10 entries
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$offset = ($page - 1) * $limit;

// Count total records
$whereClause = $search ? "WHERE title LIKE '%$search%' OR description LIKE '%$search%'" : "";
$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM announcements $whereClause");
$totalRecords = mysqli_fetch_assoc($totalQuery)['total'];
$totalPages = ceil($totalRecords / $limit);

// Fetch paginated records
$sql = "SELECT * FROM announcements $whereClause ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);

?>

<style>
    .content-wrapper {
        background: #f4f6f9;
        padding: 20px 30px;
        min-height: calc(100vh - 56px);
    }

    h2 {
        font-weight: 700;
        color: #343a40;
    }

    img.announcement-img {
        max-width: 120px;
        max-height: 90px;
        border-radius: 6px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    .btn-action {
        margin: 0 2px;
    }
</style>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include 'navbar.php'; ?>

        <div class="content-wrapper">
            <section class="content-header mb-3 d-flex justify-content-between align-items-center">
                <h2>Announcements</h2>
                <button id="btnAddAnnouncement" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> Add Announcement
                </button>
            </section>

            <section class="content">
                <div class="d-flex justify-content-between mb-3">
                    <!-- 🔍 Search -->
                    <form method="GET" class="d-flex">
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control me-2" placeholder="Search announcements...">
                        <button type="submit" class="btn btn-dark">Search</button>
                    </form>

                    <!-- 🔽 Show # entries -->
                    <form method="GET" class="d-flex align-items-center">
                        <label class="me-2 my-auto">Show</label>
                        <select name="limit" class="form-select me-3" style="width: 60px;" onchange="this.form.submit()">
                            <option value="5" <?= $limit == 5 ? 'selected' : '' ?>>5</option>
                            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25</option>
                            <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                        </select>
                        <label class="my-auto">entries</label>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    </form>

                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="alertBox"></div>

                            <table id="announcementsTable" class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width:25%;">Title</th>
                                        <th>Description</th>
                                        <th style="width:20%;">Image</th>
                                        <th style="width:15%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($announcement = mysqli_fetch_assoc($result)) : ?>
                                        <tr data-id="<?= $announcement['id'] ?>"
                                            data-title="<?= htmlspecialchars($announcement['title'], ENT_QUOTES) ?>"
                                            data-description="<?= htmlspecialchars($announcement['description'], ENT_QUOTES) ?>"
                                            data-image_path="<?= $announcement['image_path'] ?>">

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
                                                <button class="btn btn-primary btn-sm btn-action btnUpdate"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm btn-action btnDelete"><i class="fas fa-trash-alt"></i></button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>

                            <!-- 🔽 Pagination -->
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
            </section>
        </div>
        <!-- Add Announcement Modal -->
        <div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="addAnnouncementForm" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addAnnouncementLabel">Add Announcement</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Announcement Modal -->
        <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editAnnouncementForm" enctype="multipart/form-data">
                        <input type="hidden" name="id">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editAnnouncementLabel">Edit Announcement</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Current Image</label><br>
                                <img id="currentImage" src="" alt="No Image" style="max-width:120px; max-height:90px;">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Change Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete Announcement Modal -->
        <div class="modal fade" id="deleteAnnouncementModal" tabindex="-1" aria-labelledby="deleteAnnouncementLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteAnnouncementLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this announcement? This action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </div>
        </div>


        <?php include 'footer.php'; ?>
    </div>

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // ✅ Show success/error message after reload
            if (localStorage.getItem("flashMessage")) {
                $("#alertBox").html(`
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ${localStorage.getItem("flashMessage")}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
                localStorage.removeItem("flashMessage");
            }

            // 🔍 Search filter
            $("#searchInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#announcementsTable tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // 🟢 Add Announcement (open modal)
            $("#btnAddAnnouncement").on("click", function() {
                $("#addAnnouncementModal").modal("show");
            });

            // Handle Add Form Submit
            $("#addAnnouncementForm").on("submit", function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                formData.append('action', 'add_announcement');

                $.ajax({
                    url: '',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        $("#addAnnouncementModal").modal("hide");
                        localStorage.setItem("flashMessage", "✅ Announcement added successfully!");
                        location.reload();
                    },

                    error: function(xhr) {
                        alert("Error: " + xhr.responseText);
                    }
                });
            });

            // ✏️ Update Announcement (open modal with data)
            $("#announcementsTable").on("click", ".btnUpdate", function() {
                const tr = $(this).closest('tr');
                const id = tr.data('id');
                const title = tr.data('title');
                const description = tr.data('description');
                const image_path = tr.data('image_path');

                $("#editAnnouncementForm [name=id]").val(id);
                $("#editAnnouncementForm [name=title]").val(title);
                $("#editAnnouncementForm [name=description]").val(description);

                if (image_path) {
                    $("#currentImage").attr("src", image_path).show();
                } else {
                    $("#currentImage").attr("src", "").hide();
                }

                $("#editAnnouncementModal").modal("show");
            });

            // Handle Update Form Submit
            $("#editAnnouncementForm").on("submit", function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                formData.append('action', 'update_announcement');

                $.ajax({
                    url: '',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        $("#editAnnouncementModal").modal("hide");
                        localStorage.setItem("flashMessage", "✏️ Announcement updated successfully!");
                        location.reload();
                    },

                    error: function(xhr) {
                        alert("Error: " + xhr.responseText);
                    }
                });
            });

            // 🗑️ Delete Announcement (open modal)
            let deleteId = null;
            $("#announcementsTable").on("click", ".btnDelete", function() {
                deleteId = $(this).closest('tr').data('id');
                $("#deleteAnnouncementModal").modal("show");
            });

            // Confirm Delete
            $("#confirmDeleteBtn").on("click", function() {
                if (!deleteId) return;
                $.ajax({
                    url: '',
                    method: 'POST',
                    data: {
                        action: 'delete_announcement',
                        id: deleteId
                    },
                    success: function() {
                        $("#deleteAnnouncementModal").modal("hide");
                        localStorage.setItem("flashMessage", "🗑️ Announcement deleted successfully!");
                        location.reload();
                    },

                    error: function(xhr) {
                        alert("Error: " + xhr.responseText);
                    }
                });
            });

        });
    </script>

</body>