<?php
// Database connection
$host = "localhost"; // Replace with your host
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$database = "dmssystem"; // Replace with your database name

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the foundation ID to delete
if (isset($_GET["id"])) {
    $fid = $_GET["id"];

    // Delete the foundation
    $sql = "DELETE FROM foundations WHERE fid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $fid);

    if ($stmt->execute()) {
        echo "Foundation deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();

// Redirect back to the addfoundation.html page
header("Location: addfoundation.php");
exit();
?>