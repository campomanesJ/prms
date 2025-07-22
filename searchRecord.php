<?php
include 'db_connect.php';

function maskName($name)
{
    $words = explode(' ', $name);
    $maskedWords = [];
    foreach ($words as $word) {
        $len = mb_strlen($word, 'UTF-8');
        if ($len <= 2) {
            // if word has only 1 or 2 letters, just mask the middle if exists
            $maskedWords[] = $len === 2 ? mb_substr($word, 0, 1, 'UTF-8') . '*' : $word;
        } else {
            // keep first and last, middle replaced with *
            $middle = str_repeat('*', $len - 2);
            $maskedWords[] = mb_substr($word, 0, 1, 'UTF-8') . $middle . mb_substr($word, -1, 1, 'UTF-8');
        }
    }
    return implode(' ', $maskedWords);
}


$records = [];
$type = '';
$search = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type']) && $_GET['type'] != '') {
    $type = $_GET['type'];
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    if ($search !== '') {
        switch ($type) {
            case 'baptism':
                $sql = "SELECT CONCAT(COALESCE(firstname, ''), ' ', COALESCE(middlename, ''), ' ', COALESCE(lastname, '')) AS name, birthdate 
                        FROM baptism_tbl 
                        WHERE CONCAT(COALESCE(firstname, ''), ' ', COALESCE(middlename, ''), ' ', COALESCE(lastname, '')) LIKE ?";
                break;
            case 'confirmation':
                $sql = "SELECT CONCAT(COALESCE(firstname, ''), ' ', COALESCE(middlename, ''), ' ', COALESCE(lastname, '')) AS name, birthdate 
                        FROM confirmation_tbl 
                        WHERE CONCAT(COALESCE(firstname, ''), ' ', COALESCE(middlename, ''), ' ', COALESCE(lastname, '')) LIKE ?";
                break;
            case 'death':
                $sql = "SELECT CONCAT(COALESCE(firstname, ''), ' ', COALESCE(middlename, ''), ' ', COALESCE(lastname, '')) AS name, 'None' as birthdate 
                        FROM death_tbl 
                        WHERE CONCAT(COALESCE(firstname, ''), ' ', COALESCE(middlename, ''), ' ', COALESCE(lastname, '')) LIKE ?";
                break;
            case 'marriage':
                $sql = "SELECT COALESCE(husband_name, '') AS name, husband_birthdate AS birthdate 
                        FROM marriage_tbl 
                        WHERE COALESCE(husband_name, '') LIKE ?";
                break;
            default:
                $sql = '';
        }

        if ($sql) {
            $stmt = $conn->prepare($sql);
            $likeSearch = "%$search%";
            $stmt->bind_param("s", $likeSearch);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $records[] = $row;
            }
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Search Records | Parish Matalom</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background:
                linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('assets/img/church.jpg') center center / cover no-repeat fixed;
        }



        .overlay-container {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .content-below-navbar {
            padding: 80px 15px 2rem 15px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fs-4" href="index.php">Parish Matalom</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto navbar-nav">
                    <a class="nav-link ms-2 " href="index.php">Home</a>
                    <a class="nav-link ms-2 active" href="#">Search Record</a>
                    <a class="nav-link ms-2" href="announcement.php">Announcements & Events</a>
                    <a class="nav-link ms-2" href="about.php">About</a>
                    <a class="nav-link ms-2 btn btn-warning border border-white" href="login/login.php">Login</a>

                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-5 overlay-container content-below-navbar">

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="type" class="form-select" required>
                    <option value="">Select Record Type</option>
                    <option value="baptism" <?= $type == 'baptism' ? 'selected' : '' ?>>Baptism</option>
                    <option value="confirmation" <?= $type == 'confirmation' ? 'selected' : '' ?>>Confirmation</option>
                    <option value="death" <?= $type == 'death' ? 'selected' : '' ?>>Death</option>
                    <option value="marriage" <?= $type == 'marriage' ? 'selected' : '' ?>>Marriage</option>
                </select>
            </div>
            <div class="col-md-6">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search by name...">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </div>
        </form>

        <?php if ($type && count($records)): ?>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Birthdate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $rec): ?>
                        <tr>
                            <td><?= htmlspecialchars(maskName($rec['name'])) ?></td>

                            <td><?= htmlspecialchars($rec['birthdate']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($type && $search !== ''): ?>
            <div class="alert alert-warning">No records found.</div>
        <?php elseif ($type && $search === ''): ?>
            <div class="alert alert-info">Please enter a search term to find records.</div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>