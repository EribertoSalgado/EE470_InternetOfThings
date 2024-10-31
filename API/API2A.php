<html>
    <h1 style="color:green;"> IoT Course</h1>
    <h4> How to call PHP function on the click of a Button?</h4>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
        Click to turn ON: <input type="submit" name="fname" value="on">
        <p></p>
        Click to turn OFF: <input type="submit" name="fname" value="off">
    </form>
</html>
<!-- Using PHP Script: passing fname via POST_METHOD -->
<?php
    $var1 = " ";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $var1= $_POST["fname"];
        if (!empty($var1)) {
            //Show the last clicked button
			echo "Last time your clicked was: $var1<br>";
			//open the file and write only when the button is clicked
            $myfile = fopen("results.txt","w") or die("Unable to open file!");
            fwrite($myfile,$var1);
            fclose($myfile);
            }
    }
    echo "https://lightpink-sheep-430801.hostingersite.com/results.txt<br>";
    echo "Automatically reading from tp.txt file.....";
    // The Purpose of this file is read a different file and show its content.
    // URL of the file to be read
    $file_url = 'https://lightpink-sheep-430801.hostingersite.com/results.txt';
    
    //Use file_get_contents to read the file
    $file_content = file_get_contents($file_url);
    
    // Check if the file was successfully read
    if($file_content !== false) {
        //Display the content of the file
        echo nl2br($file_content); // nl2br to handle new lines in the output
        echo ".....\n";
    }    else{
            // If there was an error reading the file
            echo "Error: Could not read the file.";
        }
?>
