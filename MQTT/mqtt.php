<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection details
$servername = <>;
$username = <>;
$password = <>;
$dbname = <>;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo "Connection error.";
    die("Connection failed: " . $conn->connect_error);
}

// Insert potentiometer value if provided
$potValue = null;
foreach ($_REQUEST as $key => $value) {
    if ($key == "pot") {
        $potValue = $value;
    }
}

if (isset($potValue)) {
    $sql = "INSERT INTO `MQTT_Data`(`sensor_value`) VALUES ('" . $potValue . "')";
    if (!$conn->query($sql)) {
        echo "Error with SQL: " . $conn->error;
    }
}

// Fetch data for the Google Chart
$sql = "SELECT `timestamp`, `sensor_value` FROM `MQTT_Data` ORDER BY `timestamp` ASC";
$result = $conn->query($sql);

$tempData = [['Timestamp', 'Potentiometer Value']]; // Chart headers
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tempData[] = [$row['timestamp'], (float)$row['sensor_value']];
    }
} else {
    echo "<p>No data available or query failed.</p>";
}

// Encode data as JSON for JavaScript
$tempDataJson = json_encode($tempData);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potentiometer Data</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>
<body>
    <h1>Potentiometer Data Over Time</h1>

    <!-- Display the newest value -->
    <?php
    if (isset($potValue)) {
        echo "<p>Newest Potentiometer Value: " . htmlspecialchars($potValue) . "</p>";
    }
    ?>

    <!-- Display the chart -->
    <div id="temp_chart" style="width: 900px; height: 500px;"></div>

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            // Prepare data for the chart
            var tempData = google.visualization.arrayToDataTable(<?php echo $tempDataJson; ?>);

            // Chart options
            var tempOptions = {
                title: 'Potentiometer Data Over Time',
                curveType: 'none',
                legend: { position: 'bottom' },
                hAxis: { title: 'Timestamp' },
                vAxis: { title: 'Voltage' }
            };

            // Render the chart
            var tempChart = new google.visualization.LineChart(document.getElementById('temp_chart'));
            tempChart.draw(tempData, tempOptions);
        }
    </script>
</body>
</html>
