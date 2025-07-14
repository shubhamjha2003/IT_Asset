<?php
require '../db/connection.php';

header('Content-Type: application/json');

if (isset($_GET['emp_id'])) {
    $emp_id = trim($_GET['emp_id']);

    $stmt = $conn->prepare("SELECT role FROM users WHERE employee_id = ?");
    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($role);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();

        if ($role === 'do_not_login' || $role === 'disabled' || empty($role)) {
            echo json_encode(['status' => 'unauthorized']);
        } else {
            echo json_encode(['status' => 'authorized']);
        }
    } else {
        echo json_encode(['status' => 'not_found']);
    }
} else {
    echo json_encode(['status' => 'missing']);
}
?>
