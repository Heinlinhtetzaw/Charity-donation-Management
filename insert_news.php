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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Upload image to server
    $uploadDir = "uploads/"; // Directory to store images
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Create directory if it doesn't exist
    }

    $imagePath = $uploadDir . basename($_FILES["image"]["name"]);
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
        // Get form data
        $title = $_POST["title"];
        $content = $_POST["content"];

        // Insert data into the database
        $sql = "INSERT INTO news (title, content, image_path) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $title, $content, $imagePath);

        if ($stmt->execute()) {
            header("location: addnews.php");
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error uploading image.";
    }
}

$conn->close();
?>