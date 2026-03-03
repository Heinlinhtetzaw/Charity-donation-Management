<?php
require_once 'auth_check.php';

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
    $fid = (int) $_GET["id"];

    // Delete the foundation
    $sql = "DELETE FROM foundations WHERE fid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $fid);

    $stmt->execute();

    $stmt->close();
} else {
    header("Location: addfoundation.php");
    exit();
}

$conn->close();

// Redirect back to the addfoundation.html page
header("Location: addfoundation.php");
exit();
?>
