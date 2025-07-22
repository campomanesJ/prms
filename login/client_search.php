<?php
include "function.php";
include "db_connect.php";

$category = $_GET['category'] ?? '';
$value = $_GET['value'] ?? '';

if (!$category || !$value) {
    die("Invalid search parameters.");
}

function mask($str) {
    $words = explode(" ", $str);
    $maskedWords = [];
    foreach ($words as $word) {
        $len = strlen($word);
        if ($len <= 1) {
            $maskedWords[] = "*";
        } elseif ($len == 2) {
            $maskedWords[] = $word[0] . "*";
        } else {
            $maskedWords[] = $word[0] . str_repeat("*", $len - 2) . $word[$len - 1];
        }
    }
    return implode(" ", $maskedWords);
}
if ($category === 'marriage') {
    $words = explode(" ", $value);
    $likeClauses = [];
    $params = [];
    $types = "";

    foreach ($words as $word) {
        $likeClauses[] = "husband_name LIKE ?";
        $params[] = "%" . $word . "%";
        $types .= "s";
    }

    $sql = "SELECT * FROM marriage_tbl WHERE " . implode(" AND ", $likeClauses);
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
} else {
    $stmt = $conn->prepare("SELECT * FROM {$category}_tbl WHERE firstname LIKE ?");
    $searchTerm = "%" . $value . "%";
    $stmt->bind_param("s", $searchTerm);
}

$stmt->execute();
$result = $stmt->get_result();
$records = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($category === 'marriage') {
            $display = [
                'Husband' => mask($row['husband_name']),
                'Wife' => mask($row['wife_name'])
            ];
        } else {
            $last = isset($row['lastname']) ? mask($row['lastname']) : '';
            $display = [
                'Name' => mask($row['firstname']) . " " . $last
            ];
        }
        $records[] = $display;
    }
} else {
    $records[] = ["No records found."];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Search Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
        }
        .center-container {
            min-height: 100vh;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light d-flex align-items-center justify-content-center">

    <div class="container center-container d-flex align-items-center justify-content-center">
        <div class="card p-4 w-100" style="max-width: 700px;">
            <h4 class="text-center mb-4">
                Search Results for <strong>"<?php echo htmlspecialchars($value); ?>"</strong> in <strong><?php echo htmlspecialchars(ucfirst($category)); ?></strong>
            </h4>

            <?php if ($records && isset($records[0]['Name']) || isset($records[0]['Husband'])): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover bg-white text-center">
                        <thead class="table-dark">
                            <tr>
                                <?php foreach ($records[0] as $key => $val): ?>
                                    <th><?php echo htmlspecialchars(ucfirst($key)); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <?php foreach ($record as $col): ?>
                                        <td><?php echo htmlspecialchars($col); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">No records found.</div>
            <?php endif; ?>

            <div class="text-center mt-3">
                <a href="login.php" class="btn btn-secondary">Back to Login</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
