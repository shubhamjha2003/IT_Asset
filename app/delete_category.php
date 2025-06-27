<?php
// include('../db/connection.php');


// if (isset($_GET['id']) && is_numeric($_GET['id'])) {
//     $id = $_GET['id'];

    
//     $result = $conn->query("SELECT image_path FROM categories WHERE id = $id");
//     if ($result && $result->num_rows > 0) {
//         $row = $result->fetch_assoc();
//         $imagePath = $row['image_path'];
//         if (!empty($imagePath) && file_exists($imagePath)) {
//             unlink($imagePath); 
//         }
//     }

   
//     $conn->query("DELETE FROM categories WHERE id = $id");
// }


// header("Location: category.php?msg=deleted");
// exit;
?>


<?php
include('../db/connection.php');

// Check if ID is provided and is numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch image path using prepared statement
    $stmt = $conn->prepare("SELECT image_path FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imagePath = $row['image_path'];

        // Delete the image from the server
        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    $stmt->close();

    // Delete category using prepared statement
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Redirect to category list page
header("Location: category.php?msg=deleted");
exit;
?>

