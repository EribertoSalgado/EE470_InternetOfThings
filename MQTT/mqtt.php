<!--This code gets the potentiometer value from our database after it has 
been published from the relay node-->
<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>


<?php
$servername = "localhost";
$username = <>;
$password = <>;
$dbname = <>;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo "connection error.";
    die("Connection failed: " . $conn->connect_error);
    
}


$potValue = NULL;


foreach ($_REQUEST as $key => $value)
{
    if($key == "pot")
    {
        $potValue = $value;
        //echo $nodeParam;
        // "<br>";
    }
}


if (isset($potValue)){
    $sql = "INSERT INTO `MQTT_Data`(`sensor_value`) VALUES ('" .$potValue . "')";

    $result = $conn->query($sql);
}

?>

Text
</body>
</html>
<iframe width="450" height="260" style="border: 1px solid #cccccc;" src="https://thingspeak.com/channels/2783435/charts/1?bgcolor=%23ffffff&color=%23d62020&dynamic=true&results=60&title=Potentiometer%28V%29%29&type=line"></iframe>
