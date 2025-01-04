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

// Validate user login
if (!isset($_SESSION['user_id'])) {
  echo "You must be logged in to report incidents.";
  exit();
}

$user_id = $_SESSION['user_id'];

// Handle incident submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $incidentType = $_POST['incidentType'];
  $location = $_POST['location'];
  $description = $_POST['description'];
  $severity = $_POST['severity'];

  // Generate random incident ID in the format icd-<random_number>
  $incident_id = 'icd-' . rand(1000, 9999);

  // Assign points based on severity
  $pointsToAdd = 0;
  if ($severity === 'low') {
    $pointsToAdd = 25;
  } elseif ($severity === 'medium') {
    $pointsToAdd = 50;
  } elseif ($severity === 'high') {
    $pointsToAdd = 100;
  }

  // Insert the incident into the database
  $stmt = $conn->prepare("INSERT INTO Incidents (incident_id, user_id, type, location, description, severity) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssss", $incident_id, $user_id, $incidentType, $location, $description, $severity);

  if ($stmt->execute()) {
    // Update user points
    $updateStmt = $conn->prepare("UPDATE Users SET points = points + ? WHERE user_id = ?");
    $updateStmt->bind_param("is", $pointsToAdd, $user_id);
    $updateStmt->execute();
    $updateStmt->close();

    echo "Incident reported successfully. Points added: $pointsToAdd.";
  } else {
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
}

// Fetch incidents reported by the logged-in user
$stmt = $conn->prepare("SELECT * FROM Incidents WHERE user_id = ? ORDER BY incident_id DESC");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Reported Incidents</title>
  <link rel="stylesheet" href="style.css">
  
  
  <script>
  function display() {
    // Get values from the form fields
    const location = document.getElementById('location').value.trim();
    const description = document.getElementById('description').value.trim();
    const severity = document.getElementById('severity').value.trim();
    const incidentType = document.getElementById('incidentType').value.trim(); // Hardcoded value

    // Get the display area element
    const displayArea = document.getElementById('displayArea');

    // Build the prompt
    const prompt = `Incident Type: ${incidentType}\nLocation: ${location}\nDescription: ${description}\nSeverity: ${severity}\nSuggest the next steps. and also show the incident type and description at the start of the suggestion`;
    if(incidentType){
    // Send a POST request to the API
    fetch('http://127.0.0.1:5000/api', {
      method: 'POST',
      body: JSON.stringify({ message: prompt }),
      headers: {
        'Content-Type': 'application/json',
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.response) {
          displayArea.innerHTML = data.response;
        } else if (data.error) {
          displayArea.innerHTML = 'Error: ' + data.error;
        } else {
          displayArea.innerHTML = 'Unexpected response from the server.';
        }
      })
      .catch((error) => {
        displayArea.innerHTML = 'Failed to fetch response: ' + error.message;
      });
  }
}

  // Initialize on window load
  window.onload = () => {
    setTimeout(display, 3000); // Corrected function name
  };
</script>
</head>
<body onload="setTimeout(display, 3000)">

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
    <h1>My Reported Incidents</h1>

    <!-- Form to Report a New Incident -->
    <form method="POST">
      <label for="incidentType">Incident Type:</label>
      <input type="text" id="incidentType" name="incidentType" value="<?php echo isset($incidentType) ? htmlspecialchars($incidentType) : ''; ?>" required>
      
      <label for="location">Location (GPS or Address):</label>
      <input type="text" id="location" name="location" value="<?php echo isset($location) ? htmlspecialchars($location) : ''; ?>" required>
      
      <label for="description">Incident Description:</label>
      <textarea id="description" name="description" required><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
      
      <label for="severity">Severity Level:</label>
      <select id="severity" name="severity" required>
        <option value="low" <?php echo (isset($severity) && $severity == 'low') ? 'selected' : ''; ?>>Low</option>
        <option value="medium" <?php echo (isset($severity) && $severity == 'medium') ? 'selected' : ''; ?>>Medium</option>
        <option value="high" <?php echo (isset($severity) && $severity == 'high') ? 'selected' : ''; ?>>High</option>
      </select>
      
      <button type="submit">Submit Incident Report</button>
    </form>

  <!-- Display Area for Form Data -->
  <div id="displayArea" class="form-data-display"></div>

    <!-- Display List of User's Incidents -->
    <div class="incident-list">
      <h2>Reported Incidents</h2>
      <?php
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<div class='incident'>
                    <p><strong>Incident ID:</strong> " . $row['incident_id'] . "</p>
                    <p><strong>Type:</strong> " . $row['type'] . "</p>
                    <p><strong>Location:</strong> " . $row['location'] . "</p>
                    <p><strong>Description:</strong> " . $row['description'] . "</p>
                    <p><strong>Severity:</strong> " . $row['severity'] . "</p>
                    <p><strong>Reported At:</strong> " . $row['reported_at'] . "</p>
                  </div>";
          }
        } else {
          echo "<p>No incidents reported yet.</p>";
        }
      ?>
    </div>
  </div>
</body>
</html>
