<?php
include 'db_connect.php';

$table = $_GET['table'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$perPage = (int)($_GET['perPage'] ?? 10);
$search = $_GET['search'] ?? '';

if (!$table || !preg_match('/^[a-z_]+$/', $table)) {
    echo json_encode(['error' => 'Invalid table name.']);
    exit;
}

$offset = ($page - 1) * $perPage;
$searchSql = "";
$params = [];

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $columnsRes = $conn->query("SHOW COLUMNS FROM `$table`");
    $searchParts = [];

    while ($col = $columnsRes->fetch_assoc()) {
        $colName = $col['Field'];
        $searchParts[] = "`$colName` LIKE '%$search%'";
    }

    if (!empty($searchParts)) {
        $searchSql = "WHERE " . implode(" OR ", $searchParts);
    }
}

$data = [];
$total = 0;

// Get paginated records
$result = $conn->query("SELECT * FROM `$table` $searchSql LIMIT $perPage OFFSET $offset");
if ($result) {
    $data = $result->fetch_all(MYSQLI_ASSOC);
}

// Get total count
$countRes = $conn->query("SELECT COUNT(*) as total FROM `$table` $searchSql");
if ($countRes) {
    $total = $countRes->fetch_assoc()['total'];
}

echo json_encode(['data' => $data, 'total' => $total]);
