<?php
session_start();

// Database credentials
$servername = "sql211.infinityfree.com"; // Replace with your MySQL Hostname
$username = "if0_38039815";              // Replace with your MySQL Username
$password = "Ayush11430011";             // Replace with your MySQL Password
$dbname = "if0_38039815_incidentreport"; // Replace with your MySQL Database Name


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch user details from session
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
} else {
  echo "You must be logged in to access your profile.";
  exit();
}

// Fetch user details (user_id, username, points)
$sql = "SELECT user_id, username, points FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id); // Changed to 's' to bind as a string (VARCHAR)
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $user = $result->fetch_assoc();
} else {
  echo "User not found.";
  exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar">
  <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="myincident.php">My Reported Incidents</a></li>
            <li><a href="heatmap.php">Heatmap</a></li>
            <li><a href="leaderboard.php">Leaderboard</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
  </nav>

  <div class="container">
    <h1>User Profile</h1>
    <div class="profile">
      <!-- Display User Details -->
      <p><strong>User ID:</strong> <?php echo $user['user_id']; ?></p>
      <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
      <p><strong>Total Points:</strong> <?php echo $user['points']; ?> points</p>
    </div>
  </div>
</body>
</html>
