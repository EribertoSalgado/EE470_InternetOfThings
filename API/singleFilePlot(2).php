<?php
// PDO used to connect to database (data.php)
// Now we are connected to our database
// Query the data: grab parameters from our table
// Use parameters to create tables in HTML or Google Charts
$hostname = 'localhost';
$username = 'u537162232_db_EribertoSal';
$password = 'Erick5100';
$database = 'u537162232_EribertoSal';

try {
    // Create a PDO database connection
    $db = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode to exception

    // Check if we have POST parameters to insert data
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nodeId']) && isset($_POST['nodeTemp'])) {
        $nodeId = $_POST['nodeId'];
        $nodeTemp = $_POST['nodeTemp'];
        $hall = isset($_POST['hall']) ? $_POST['hall'] : null; // Optional hall parameter (maps to humidity)

        // Check if the node is registered
        $checkNodeQuery = "SELECT COUNT(*) FROM sensor_register WHERE node_name = :nodeId";
        $stmt = $db->prepare($checkNodeQuery);
        $stmt->bindParam(':nodeId', $nodeId);
        $stmt->execute();

        if ($stmt->fetchColumn() > 0) {
            // Insert data if the node is registered
            $insertQuery = "INSERT INTO sensor_data (node_name, temperature, humidity) VALUES (:nodeId, :nodeTemp, :humidity)";
            $stmt = $db->prepare($insertQuery);
            $stmt->bindParam(':nodeId', $nodeId);
            $stmt->bindParam(':nodeTemp', $nodeTemp);
            $stmt->bindParam(':humidity', $hall); // Bind hall as humidity
            $stmt->execute();
            
            echo "Data successfully inserted.";
        } else {
            echo "Error: Node is not registered.";
        }
    }

    // Check if we have GET parameters to insert data
    if (isset($_GET['nodeId']) && isset($_GET['nodeTemp'])) {
        $nodeId = $_GET['nodeId'];
        $nodeTemp = $_GET['nodeTemp'];
        $hall = isset($_GET['hall']) ? $_GET['hall'] : null; // Accept hall as GET parameter
    
        // Capture timeReceived, default to current time if not provided
        $timeReceived = isset($_GET['timeReceived']) ? $_GET['timeReceived'] : date('Y-m-d H:i:s'); // Default to current time
    
        // Check if the node is registered
        $checkNodeQuery = "SELECT COUNT(*) FROM sensor_register WHERE node_name = :nodeId";
        $stmt = $db->prepare($checkNodeQuery);
        $stmt->bindParam(':nodeId', $nodeId);
        $stmt->execute();
    
        if ($stmt->fetchColumn() > 0) {
            // Insert data if the node is registered
            $insertQuery = "INSERT INTO sensor_data (node_name, time_received, temperature, humidity) VALUES (:nodeId, :timeReceived, :nodeTemp, :humidity)";
            $stmt = $db->prepare($insertQuery);
            $stmt->bindParam(':nodeId', $nodeId);
            $stmt->bindParam(':timeReceived', $timeReceived); // Bind timeReceived
            $stmt->bindParam(':nodeTemp', $nodeTemp);
            $stmt->bindParam(':humidity', $hall); // Bind hall as humidity
            $stmt->execute();
    
            echo "Data successfully inserted.";
        } else {
            echo "Error: Node is not registered.";
        }
    }

    // Query to get data from sensor_data table
    $sensorDataQuery = "SELECT node_name, time_received, temperature, humidity AS hall FROM sensor_data"; // Aliasing humidity as hall for output
    $stmt1 = $db->prepare($sensorDataQuery);
    $stmt1->execute();
    $sensorData = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    // Query to get data from sensor_register table
    $sensorRegisterQuery = "SELECT node_name, manufacturer, longitude, latitude FROM sensor_register";
    $stmt2 = $db->prepare($sensorRegisterQuery);
    $stmt2->execute();
    $sensorRegister = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Query to get average temperature and hall (humidity) for "Node 1"
    $averageQuery = "SELECT AVG(temperature) AS avg_temperature, AVG(humidity) AS avg_hall FROM sensor_data WHERE node_name = 'Node_1'"; // Alias humidity as avg_hall
    $stmt3 = $db->prepare($averageQuery);
    $stmt3->execute();
    $averages = $stmt3->fetch(PDO::FETCH_ASSOC);

    // Prepare data for Google Charts
    $tempDataArray = [['Time Received', 'Temperature']];
    $humidityDataArray = [['Time Received', 'Hall']];
    $dataQuery = "SELECT time_received, temperature, humidity FROM sensor_data WHERE node_name = 'Node_1'";
    $stmt4 = $db->prepare($dataQuery);
    $stmt4->execute();
    $data = $stmt4->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as $row) {
        $tempDataArray[] = [$row['time_received'], (float) $row['temperature']];
        $humidityDataArray[] = [$row['time_received'], (float) $row['humidity']];
    }

    // Convert both temperature and humidity data to JSON
    $tempDataJson = json_encode($tempDataArray);
    $humidityDataJson = json_encode($humidityDataArray);
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensor Data and Register Tables</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 45%;
            margin: 20px;
            border-collapse: collapse;
            float: left; /* Display tables side by side */
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        h2 {
            text-align: center;
        }
        .average {
            margin-top: 20px;
            clear: both;
        }
        #temp_chart, #humidity_chart, #temp_column_chart, #humidity_column_chart {
            width: 50%; 
            height: 400px; 
            float: left;
        }
    </style>
</head>
<body>

    <h2>Welcome to SSU IoT Lab</h2>
    <h2 style='text-align:left'>Sensor Data Table</h2>

    <table>
        <thead>
            <tr>
                <th>Node Name</th>
                <th>Time Received</th>
                <th>Temperature (°F)</th>
                <th>Hall (%)</th> <!-- Display as 'Hall' -->
            </tr>
        </thead>
    <tbody>
        <?php foreach ($sensorData as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['node_name']); ?></td>
                <td><?php echo date('Y-m-d H:i:s', strtotime($row['time_received'])); ?></td> <!-- Format the time here -->
                <td><?php echo htmlspecialchars($row['temperature']); ?></td>
                <td><?php echo htmlspecialchars($row['hall']); ?></td> <!-- Refer to humidity as hall -->
            </tr>
        <?php endforeach; ?>
    </tbody>

    </table>

    <h2>Sensor Register Table</h2>
    <table>
        <thead>
            <tr>
                <th>Node Name</th>
                <th>Manufacturer</th>
                <th>Longitude</th>
                <th>Latitude</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sensorRegister as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['node_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['manufacturer']); ?></td>
                    <td><?php echo htmlspecialchars($row['longitude']); ?></td>
                    <td><?php echo htmlspecialchars($row['latitude']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Display the averages for Node 1 -->
    <div class="average">
        <h2>Average Data for Node 1</h2>
        <p>The Average Temperature for Node 1: <?php echo htmlspecialchars($averages['avg_temperature']); ?>°F</p>
        <p>The Average Hall for Node 1: <?php echo htmlspecialchars($averages['avg_hall']); ?>%</p>
    </div>

    <!-- Load Google Charts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            // Prepare Temperature Data
            var tempData = google.visualization.arrayToDataTable(<?php echo $tempDataJson; ?>);
            var tempOptions = {
                title: 'Temperature Over Time',
                //curveType: 'function',
                legend: { position: 'bottom' }
            };
            var tempChart = new google.visualization.LineChart(document.getElementById('temp_chart'));
            tempChart.draw(tempData, tempOptions);

            // Prepare Humidity Data
            var humidityData = google.visualization.arrayToDataTable(<?php echo $humidityDataJson; ?>);
            var humidityOptions = {
                title: 'Hall Over Time',
                //curveType: 'function',
                legend: { position: 'bottom' }
            };
            var humidityChart = new google.visualization.LineChart(document.getElementById('humidity_chart'));
            humidityChart.draw(humidityData, humidityOptions);

            // Prepare Temperature Column Chart
            var tempColumnData = google.visualization.arrayToDataTable(<?php echo $tempDataJson; ?>);
            var tempColumnOptions = {
                title: 'Temperature Column Chart',
                legend: { position: 'none' },
                hAxis: { title: 'Time Received' },
                vAxis: { title: 'Temperature (°F)' }
            };
            var tempColumnChart = new google.visualization.ColumnChart(document.getElementById('temp_column_chart'));
            tempColumnChart.draw(tempColumnData, tempColumnOptions);

            // Prepare Humidity Column Chart
            var humidityColumnData = google.visualization.arrayToDataTable(<?php echo $humidityDataJson; ?>);
            var humidityColumnOptions = {
                title: 'Hall Column Chart',
                legend: { position: 'none' },
                hAxis: { title: 'Time Received' },
                vAxis: { title: 'Hall (%)' }
            };
            var humidityColumnChart = new google.visualization.ColumnChart(document.getElementById('humidity_column_chart'));
            humidityColumnChart.draw(humidityColumnData, humidityColumnOptions);
        }
    </script>

    <div id="temp_chart"></div>
    <div id="humidity_chart"></div>
    <div id="temp_column_chart"></div>
    <div id="humidity_column_chart"></div>
    
    <iframe width="539" height="333" seamless frameborder="0" scrolling="no" src="https://docs.google.com/spreadsheets/d/e/2PACX-1vQLsCdeNW3Om4vrDVcmHiqP5xuAXlAH7vh0XRYhP8dQXRNVRwbmmV5hWx2I_wb3wS2ohOPLmeTXW5HC/pubchart?oid=1572675605&amp;format=interactive"></iframe>
    <iframe width="600" height="217" seamless frameborder="0" scrolling="no" src="https://docs.google.com/spreadsheets/d/e/2PACX-1vQLsCdeNW3Om4vrDVcmHiqP5xuAXlAH7vh0XRYhP8dQXRNVRwbmmV5hWx2I_wb3wS2ohOPLmeTXW5HC/pubchart?oid=592247769&amp;format=interactive"></iframe> 

</body>
</html>
