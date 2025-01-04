// Updated signin.php
<?php
session_start();
$servername = "sql211.infinityfree.com"; // Replace with your MySQL Hostname
$username = "if0_38039815";              // Replace with your MySQL Username
$password = "Ayush11430011";             // Replace with your MySQL Password
$dbname = "if0_38039815_incidentreport"; // Replace with your MySQL Database Name

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Check if user exists
  $sql = "SELECT * FROM Users WHERE username = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verify the password using password_verify()
    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['user_id']; // Store user_id in session
      header("Location: home.php"); // Redirect to home page after successful sign-in
      exit();
    } else {
      echo "Invalid username or password.";
    }
  } else {
    echo "User not found.";
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
  <title>Sign In</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="auth-container">
    <h2>Sign In</h2>
    <form method="POST" action="signin.php">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required>
      
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>
      
      <button type="submit">Sign In</button>
    </form>
    
    <p>New User? <a href="signup.php">Sign Up</a></p>
  </div>
</body>
</html>