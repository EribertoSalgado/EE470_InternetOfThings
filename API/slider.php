<!DOCTYPE html>
<html>
<body>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <!-- Slider 1: Control Red Channel -->
        <label for="slider1">Red (0-255): </label>
        <input type="range" id="slider1" name="slider1" min="0" max="255" value="0" oninput="this.nextElementSibling.value = this.value">
        <output>0</output>
        <p></p>
        
        <!-- Slider 2: Control Green Channel -->
        <label for="slider2">Green (0-255): </label>
        <input type="range" id="slider2" name="slider2" min="0" max="255" value="0" oninput="this.nextElementSibling.value = this.value">
        <output>0</output>
        <p></p>
        
        <!-- Slider 3: Control Blue Channel -->
        <label for="slider3">Blue (0-255): </label>
        <input type="range" id="slider3" name="slider3" min="0" max="255" value="0" oninput="this.nextElementSibling.value = this.value">
        <output>0</output>
        <p></p>
        
        <!-- Submit button -->
        <input type="submit" value="Submit Values">
    </form>

    <?php
    // Only process the form and write to the file if the request method is POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the values from the sliders
        $slider1_value = isset($_POST["slider1"]) ? $_POST["slider1"] : 0;
        $slider2_value = isset($_POST["slider2"]) ? $_POST["slider2"] : 0;
        $slider3_value = isset($_POST["slider3"]) ? $_POST["slider3"] : 0;

        // Display the selected values on the page
        echo "Red Value: " . $slider1_value . "<br>";
        echo "Green Value: " . $slider2_value . "<br>";
        echo "Blue Value: " . $slider3_value . "<br>";
        echo "Check the file: <a href='https://lightpink-sheep-430801.hostingersite.com/tp2.txt'>tp2.txt</a>";

        // Create an associative array for JSON format
        $data = array(
            "Red" => (int)$slider1_value,
            "Green" => (int)$slider2_value,
            "Blue" => (int)$slider3_value
        );

        // Write the values to the file in JSON format
        $json_data = json_encode($data, JSON_PRETTY_PRINT);
        $myfile = fopen("tp2.txt", "w") or die("Unable to open file!");
        fwrite($myfile, $json_data);
        fclose($myfile);
    }
    ?>
</body>
</html>
