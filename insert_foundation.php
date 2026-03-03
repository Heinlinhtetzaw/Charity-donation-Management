<?php
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
        $fname = $_POST["fname"];
        $description = $_POST["description"];
        $intro = $_POST["intro"];
        // Insert data into the database
        $sql = "INSERT INTO foundations (image_path, fname, description, intro) VALUES (?, ?, ?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $imagePath, $fname, $description, $intro);

        if ($stmt->execute()) {
            header("location: addfoundation.php");
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