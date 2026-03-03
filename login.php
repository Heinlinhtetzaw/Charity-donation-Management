<?php
session_start();
$con=mysqli_connect('localhost','root','','dmssystem');

if (!$con) {
    die("Connection failed: " .mysqli_error($con));
}

// Get form data
$username = $_POST['username'];
$password = ($_POST['password']);

$query = "SELECT * FROM admin WHERE adname='$username' AND adpassword='$password'";
$result = mysqli_query($con,$query);

        if (mysqli_num_rows($result)> 0) {
            $_SESSION['admin_password']= $password;
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            header("Location: addashboard.php"); // Redirect to admin panel
            exit();
        } else {
           $_SESSION['error']="Invalid username or password!";
           header("Location: adlogin.php");
        }


?>
