
// Updated signup.php
<?php
// Database connection
$servername = "sql211.infinityfree.com"; // Replace with your MySQL Hostname
$username = "if0_38039815";              // Replace with your MySQL Username
$password = "Ayush11430011";             // Replace with your MySQL Password
$dbname = "if0_38039815_incidentreport"; // Replace with your MySQL Database Name

$conn = new mysqli($servername, $username, $password, $dbname);

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security
    $user_id = "usr-" . rand(1000, 9999); // Generate random user ID

    // Insert data into the database
    $sql = "INSERT INTO Users (user_id, username, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $user_id, $username, $password);

    if ($stmt->execute()) {
        echo "New user created successfully. You can now sign in.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>
<body>
    <h2>Sign Up</h2>
    <form method="POST" action="signup.php">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br><br>
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br><br>
        
        <button type="submit">Sign Up</button>
    </form>
</body>
</html>
