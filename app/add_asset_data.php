<?php
include('../db/connection.php');

// Get all custom fields
$result = $conn->query("SELECT id, field_name FROM custom_fields");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Asset Data</title>
</head>
<body>
    <h2>Add Asset Data</h2>
    <form method="POST" action="save_asset_data.php">
        <label>Choose Field:</label>
        <select name="custom_field_id" required>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <option value="<?= $row['id'] ?>">
                    <?= htmlspecialchars($row['field_name']) ?>
                </option>
            <?php } ?>
        </select><br><br>

        <label>Value:</label>
        <input type="text" name="value" required><br><br>

        <button type="submit">Save Data</button>
    </form>
</body>
</html>
