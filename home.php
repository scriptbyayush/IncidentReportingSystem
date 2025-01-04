<?php
session_start();

// Database credentials
// $servername = "localhost";
// $username = "root"; // Default username for XAMPP
// $password = ""; // Default password for XAMPP
// $dbname = "incident_reporting";

$servername = "sql211.infinityfree.com"; // Replace with your MySQL Hostname
$username = "if0_38039815";              // Replace with your MySQL Username
$password = "Ayush11430011";             // Replace with your MySQL Password
$dbname = "if0_38039815_incidentreport"; // Replace with your MySQL Database Name


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate user login
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to access this page.";
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $incidentID = $_POST['incidentID'];
    $comment = $conn->real_escape_string($_POST['comment']);

    $sql = "INSERT INTO Comments (user_id, incident_id, comment) VALUES ('$user_id', '$incidentID', '$comment')";

    if ($conn->query($sql) === TRUE) {
        echo "Comment added successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident Reporting and Response System</title>
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
        <h1>Incident Reporting and Response System</h1>

        <div class="incident-list">
            <h2>Reported Incidents</h2>
            <?php
            // Fetch incidents along with reporting user's user_id
            $sql = "
                SELECT 
                    i.incident_id, 
                    i.type, 
                    i.location, 
                    i.severity, 
                    i.description, 
                    i.user_id AS reported_by, 
                    i.reported_at 
                FROM Incidents i
                ORDER BY i.incident_id DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $incidentID = $row['incident_id'];
                    echo "<div class='incident'>
                            <p><strong>ID:</strong> " . $row['incident_id'] . "</p>
                            <p><strong>Type:</strong> " . $row['type'] . "</p>
                            <p><strong>Location:</strong> " . $row['location'] . "</p>
                            <p><strong>Description:</strong> " . $row['description'] . "</p>
                            <p><strong>Severity:</strong> " . $row['severity'] . "</p>
                            <p><strong>Reported By:</strong> " . $row['reported_by'] . "</p>
                            <p><strong>Reported At:</strong> " . $row['reported_at'] . "</p>";

                    // Fetch comments for this incident
                    $commentSql = "
                        SELECT c.comment, c.commented_at, c.user_id
                        FROM Comments c
                        WHERE c.incident_id = '$incidentID'";
                    $commentResult = $conn->query($commentSql);

                    if ($commentResult->num_rows > 0) {
                        echo "<h4>Comments:</h4>";
                        while ($commentRow = $commentResult->fetch_assoc()) {
                            echo "<p><strong>" . $commentRow['user_id'] . ":</strong> " . $commentRow['comment'] . " <em>(" . $commentRow['commented_at'] . ")</em></p>";
                        }
                    } else {
                        echo "<p>No comments yet.</p>";
                    }

                    // Comment form
                    echo "
                        <form method='POST' action='home.php'>
                            <textarea name='comment' placeholder='Add a comment' required></textarea><br>
                            <input type='hidden' name='incidentID' value='$incidentID'>
                            <button type='submit'>Submit Comment</button>
                        </form>
                    </div>";
                }
            } else {
                echo "<p>No incidents reported yet.</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
