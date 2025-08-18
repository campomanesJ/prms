<?php

include 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['login_id'])) {
    header("Location: ../index.php");
    exit;
}

function fetchAll($conn, $table)
{
    $result = $conn->query("SELECT * FROM $table");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function formatColumnName($column)
{
    return ucwords(str_replace('_', ' ', $column));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View PRMS Data</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        <?php include 'navbar.php'; ?>

        <div class="content-wrapper" style="margin-top: 20px;">
            <div class="container">
                <h2 class="fw-bold mb-4" style="font-size: 30px; text-align: left;">PRMS Database Records</h2>

                <ul class="nav nav-tabs" id="dataTabs" role="tablist">
                    <?php
                    $tabs = ['baptism' => 'Baptism', 'confirmation' => 'Confirmation', 'death' => 'Death', 'marriage' => 'Marriage'];
                    $isFirst = true;
                    foreach ($tabs as $key => $label): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= $isFirst ? 'active' : '' ?>" id="<?= $key ?>-tab" data-bs-toggle="tab"
                                data-bs-target="#<?= $key ?>" type="button"><?= $label ?></button>
                        </li>
                    <?php $isFirst = false;
                    endforeach; ?>
                </ul>

                <div class="tab-content mt-4" id="dataTabsContent">
                    <?php
                    $tables = [
                        'baptism' => 'baptism_tbl',
                        'confirmation' => 'confirmation_tbl',
                        'death' => 'death_tbl',
                        'marriage' => 'marriage_tbl'
                    ];

                    $isFirst = true;
                    foreach ($tables as $key => $tableName):
                        $records = fetchAll($conn, $tableName);
                        $tableId = $key . "_table";
                    ?>
                        <div class="tab-pane fade <?= $isFirst ? 'show active' : '' ?>" id="<?= $key ?>" role="tabpanel">
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="me-2">Show</label>
                                    <select class="form-select d-inline-block w-auto entries-select" data-table-id="<?= $tableId ?>">
                                        <option value="5">5</option>
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                    </select>
                                    <span class="ms-2">entries</span>
                                </div>
                                <div>
                                    <input type="text" class="form-control search-input" data-table-id="<?= $tableId ?>" placeholder="Search...">
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="<?= $tableId ?>" class="table table-bordered table-striped table-sm">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Action</th>
                                            <?php
                                            if (!empty($records)) {
                                                $columns = array_keys($records[0]);

                                                // Force order: id, book_no, page_no, then the rest
                                                $orderedCols = [];
                                                if (in_array('id', $columns)) $orderedCols[] = 'id';
                                                if (in_array('book_no', $columns)) $orderedCols[] = 'book_no';
                                                if (in_array('page_no', $columns)) $orderedCols[] = 'page_no';

                                                foreach ($columns as $col) {
                                                    if (!in_array($col, ['id', 'book_no', 'page_no'])) {
                                                        $orderedCols[] = $col;
                                                    }
                                                }

                                                foreach ($orderedCols as $col) {
                                                    echo "<th>" . htmlspecialchars(formatColumnName($col)) . "</th>";
                                                }
                                                echo "<script>window.tableColumns = window.tableColumns || {}; 
      window.tableColumns['$tableId'] = " . json_encode($orderedCols) . ";</script>";
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody id="<?= $tableId ?>_body"></tbody>

                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div class="flex-grow-1 text-end">
                                    <small class="text-muted total-records" data-table-id="<?= $tableId ?>"></small>
                                </div>
                            </div>
                            <nav class="d-flex justify-content-center mt-1">
                                <ul class="pagination mb-0" data-table-id="<?= $tableId ?>"></ul>
                            </nav>


                        </div>
                    <?php $isFirst = false;
                    endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form method="POST" class="modal-content shadow-lg rounded-3 border-0">

                <!-- Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-2"></i> Edit Record
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div id="editModalBody" class="row g-3">
                        <!-- Dynamic form fields will load here -->
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer d-flex justify-content-between">
                    <input type="hidden" name="table" id="editTableName">
                    <input type="hidden" name="id" id="editRecordId">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary fw-semibold">
                        <i class="bi bi-save me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>


    <?php
    // Update record
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table']) && isset($_POST['id']) && !isset($_POST['delete_id'])) {
        $table = $_POST['table'];
        $id = $_POST['id'];
        unset($_POST['table'], $_POST['id']);

        $updates = [];
        foreach ($_POST as $key => $value) {
            $value = $conn->real_escape_string($value);
            $updates[] = "`$key` = '$value'";
        }

        $sql = "UPDATE `$table` SET " . implode(", ", $updates) . " WHERE `id` = '$id'";
        $conn->query($sql);

        echo "<script>window.location='view_data.php';</script>";
    }

    // Delete record
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'], $_POST['delete_table'])) {
        $id = $conn->real_escape_string($_POST['delete_id']);
        $table = $conn->real_escape_string($_POST['delete_table']);
        $conn->query("DELETE FROM `$table` WHERE `id` = '$id'");
        exit;
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function setupServerTable(tableId, tableName) {
            const tbody = document.getElementById(`${tableId}_body`);
            const searchInput = document.querySelector(`.search-input[data-table-id="${tableId}"]`);
            const entriesSelect = document.querySelector(`.entries-select[data-table-id="${tableId}"]`);
            const pagination = document.querySelector(`.pagination[data-table-id="${tableId}"]`);

            let currentPage = 1;
            let entriesPerPage = parseInt(entriesSelect.value);
            let searchTerm = '';

            function fetchData() {
                $.get('fetch_data.php', {
                    table: tableName,
                    page: currentPage,
                    perPage: entriesPerPage,
                    search: searchTerm
                }, function(res) {
                    if (res.error) {
                        tbody.innerHTML = `<tr><td colspan="100%">Error: ${res.error}</td></tr>`;
                        return;
                    }

                    renderTable(res.data);
                    renderPagination(res.total);
                    document.querySelector(`.total-records[data-table-id="${tableId}"]`).textContent = `Total records: ${res.total}`;

                }, 'json');
            }

            function renderTable(data) {
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="100%">No records found.</td></tr>';
                    return;
                }

                data.forEach(row => {
                    const tr = document.createElement('tr');

                    // Action buttons
                    let actions = `
        <td>
            <div class='d-flex gap-1'>
                <button class='btn btn-sm btn-warning edit-btn' 
                        data-id='${row.id}' 
                        data-table='${tableName}' 
                        data-record='${JSON.stringify(row).replace(/'/g, "&apos;")}'}>
                    Edit
                </button>
                <button class='btn btn-sm btn-danger delete-btn' 
                        data-id='${row.id}' 
                        data-table='${tableName}'>
                    Delete
                </button>
                <button class='btn btn-sm btn-success generate-cert-btn' 
                        data-id='${row.id}'>
                    PDF
                </button>
            </div>
        </td>`;

                    tr.innerHTML = actions;

                    // Correct order of columns
                    const columns = window.tableColumns[tableId];
                    columns.forEach(col => {
                        tr.innerHTML += `<td>${row[col] !== undefined ? row[col] : ''}</td>`;
                    });

                    tbody.appendChild(tr);
                });
            }


            function renderPagination(total) {
                pagination.innerHTML = '';
                const totalPages = Math.ceil(total / entriesPerPage);

                // Previous button
                const prevLi = document.createElement("li");
                prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#">Previous</a>`;
                prevLi.addEventListener("click", function(e) {
                    e.preventDefault();
                    if (currentPage > 1) {
                        currentPage--;
                        fetchData();
                    }
                });
                pagination.appendChild(prevLi);

                // Page numbers (you can limit range if many pages)
                const maxVisiblePages = 5;
                let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
                if (endPage - startPage < maxVisiblePages - 1) {
                    startPage = Math.max(1, endPage - maxVisiblePages + 1);
                }

                for (let i = startPage; i <= endPage; i++) {
                    const li = document.createElement("li");
                    li.className = `page-item ${i === currentPage ? 'active' : ''}`;
                    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    li.addEventListener("click", function(e) {
                        e.preventDefault();
                        currentPage = i;
                        fetchData();
                    });
                    pagination.appendChild(li);
                }

                // Next button
                const nextLi = document.createElement("li");
                nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="#">Next</a>`;
                nextLi.addEventListener("click", function(e) {
                    e.preventDefault();
                    if (currentPage < totalPages) {
                        currentPage++;
                        fetchData();
                    }
                });
                pagination.appendChild(nextLi);
            }


            // Event listeners
            searchInput.addEventListener("input", function() {
                searchTerm = this.value;
                currentPage = 1;
                fetchData();
            });

            entriesSelect.addEventListener("change", function() {
                entriesPerPage = parseInt(this.value);
                currentPage = 1;
                fetchData();
            });

            fetchData();
        }


        document.addEventListener("DOMContentLoaded", function() {
            // Setup tables with server-side pagination
            setupServerTable('baptism_table', 'baptism_tbl');
            setupServerTable('confirmation_table', 'confirmation_tbl');
            setupServerTable('death_table', 'death_tbl');
            setupServerTable('marriage_table', 'marriage_tbl');

            // Edit button
            $(document).on('click', '.edit-btn', function() {
                const record = JSON.parse($(this).attr('data-record'));
                const modalBody = $('#editModalBody').html('');
                $('#editTableName').val($(this).attr('data-table'));
                $('#editRecordId').val($(this).attr('data-id'));

                for (const key in record) {
                    const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    let inputField = '';

                    if (
                        key === 'suffix' ||
                        key === 'husband_fatherName_suffix' ||
                        key === 'wife_fatherName_suffix' ||
                        key === 'relative_suffix' ||
                        key === 'father_suffix'
                    ) {
                        inputField = `
                    <select class="form-select" name="${key}">
                        <option value="">None</option>
                        <option value="Jr." ${record[key] === 'Jr.' ? 'selected' : ''}>Jr.</option>
                        <option value="Sr." ${record[key] === 'Sr.' ? 'selected' : ''}>Sr.</option>
                        <option value="III" ${record[key] === 'III' ? 'selected' : ''}>III</option>
                        <option value="IV" ${record[key] === 'IV' ? 'selected' : ''}>IV</option>
                    </select>
                `;
                    } else if (
                        key === 'birthdate' ||
                        key === 'baptism_date' ||
                        key === 'confirmed_date' ||
                        key === 'husband_birthdate' ||
                        key === 'wife_birthdate' ||
                        key === 'marriage_date' ||
                        key === 'death_date'
                    ) {
                        inputField = `<input type="date" class="form-control" name="${key}" value="${record[key]}">`;
                    } else {
                        inputField = `<input type="text" class="form-control" name="${key}" value="${record[key]}" ${key === 'id' ? 'readonly' : ''}>`;
                    }

                    modalBody.append(`
                <div class="mb-3">
                    <label class="form-label">${label}</label>
                    ${inputField}
                </div>
            `);
                }

                new bootstrap.Modal(document.getElementById('editModal')).show();
            });

            // Delete button
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).attr('data-id');
                const table = $(this).attr('data-table');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You can't undo this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('', {
                            delete_id: id,
                            delete_table: table
                        }, function() {
                            Swal.fire('Deleted!', 'Record has been deleted.', 'success').then(() => {
                                location.reload();
                            });
                        }).fail(() => {
                            Swal.fire('Error', 'Delete failed.', 'error');
                        });
                    }
                });
            });

            // PDF button
            $(document).on('click', '.generate-cert-btn', function() {
                Swal.fire({
                    title: 'Placeholder',
                    text: 'This is a placeholder for generating certificates.',
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
            });
        });
    </script>
</body>

</html>