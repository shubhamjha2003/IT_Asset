<?php
function logActivity($conn, $userId, $action) {
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $action);
    $stmt->execute();
    $stmt->close();
}
?>
