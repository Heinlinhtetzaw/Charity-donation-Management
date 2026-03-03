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

// Get the news ID to delete
if (isset($_GET["id"])) {
    $nid = (int) $_GET["id"];

    // Delete the news
    $sql = "DELETE FROM news WHERE nid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $nid);

    $stmt->execute();

    $stmt->close();
} else {
    header("Location: addnews.php");
    exit();
}

$conn->close();

// Redirect back to the addnews.html page
header("Location: addnews.php");
exit();
?>
