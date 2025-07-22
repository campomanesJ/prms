<?php
session_start();

include 'db_connect.php';


if (isset($_POST['btn-save'])) {

    $routing_number = $_POST['routing_number'];
    $full_name = $_POST['full_name'];
    $particular = $_POST['particular'];
    $date_time_released_from_cenro = $_POST['date_time_released_from_cenro'];
    $date_time_released_from = $_POST['date_time_released_from'];
    $responsible_person = $_POST['responsible_person'];
    $remarks = $_POST['remarks'];
    $sections = $_POST['sections'];

    $sql = "INSERT INTO cenro_release_info (routing_number, full_name, particular, date_time_released_from_cenro, date_time_released_from, responsible_person, remarks, sections) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssssss", $routing_number, $full_name, $particular, $date_time_released_from_cenro, $date_time_released_from, $responsible_person, $remarks, $sections);

        if ($stmt->execute()) {
            echo "<script>alert('Data saved successfully!'); window.location.href = 'app_entry.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
    $conn->close();
}

if (isset($_POST['create-user'])) {
}

if (isset($_POST['btn-delete'])) {
}
