<!--This code gets the potentiometer value from our database after it has 
been published from the relay node-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MQTT Data Insertion</title>
</head>
<body>

<?php
$servername = "localhost";
$username = "u537162232_db_EribertoSal";
$password = "Erick5100";
$dbname = "u537162232_EribertoSal";

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$potValue = $_GET['pot'] ?? null; // Use null coalescing to get 'pot' parameter

if ($potValue) {
    // Use prepared statement to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO `MQTT_Data` (`sensor_value`, `timestamp`) VALUES (?, NOW())");
    $stmt->bind_param("s", $potValue);

    if ($stmt->execute()) {
        echo "New record created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "No potentiometer value provided.";
}

$conn->close();
?>

</body>
</html>
