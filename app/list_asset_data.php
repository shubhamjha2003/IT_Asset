<?php
include('../db/connection.php');

$key = "secretkey";

$sql = "
    SELECT ad.id, cf.field_name, cf.encrypt_value, ad.value
    FROM asset_data ad
    JOIN custom_fields cf ON ad.custom_field_id = cf.id
";
$result = $conn->query($sql);

echo "<h2>Asset Data</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Field</th><th>Value</th></tr>";

while ($row = $result->fetch_assoc()) {
    $value = $row['value'];
    if ($row['encrypt_value']) {
        $value = openssl_decrypt($value, "AES-128-ECB", $key);
    }
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['field_name']}</td>
        <td>" . htmlspecialchars($value) . "</td>
    </tr>";
}

echo "</table>";
