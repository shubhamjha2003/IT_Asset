<?php
// components/log_activity.php

function logActivity($conn, $user_id, $action) {
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $action);
    $stmt->execute();
}
