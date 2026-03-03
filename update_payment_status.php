<?php
require_once 'auth_check.php';

// Database connection
$host = "localhost"; 
$username = "root";
$password = ""; 
$database = "dmssystem"; 

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get donation ID and action (complete or pending)
if (isset($_GET["id"]) && isset($_GET["action"])) {
    $id = (int) $_GET["id"];
    $action = $_GET["action"];

    // Validate the action
    if ($action != "complete" && $action != "pending") {
        die("Invalid action.");
    }

    // Prepare the SQL query based on the action
    if ($action == "complete") {
        // Update status to "Complete"
        $sql = "UPDATE donations SET payment_status = 'Complete' WHERE id = ?";
    } elseif ($action == "pending") {
        // Update status to "Pending"
        $sql = "UPDATE donations SET payment_status = 'Pending' WHERE id = ?";
    }

    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: adddonationstatus.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
