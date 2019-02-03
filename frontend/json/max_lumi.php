<?php

ob_start("ob_gzhandler");

$servername = "localhost";
$username = "universe";
$password = "F7V4Z50Jl7HyKFI8";
$dbname = "universe";
$array = array();


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
// echo "Connected successfully<br><br>";

$sql = "SELECT Sun_Luminosity 
        FROM suns 
        WHERE Sun_Luminosity < (select max(Sun_Luminosity) from suns)
        ORDER BY Sun_Luminosity DESC LIMIT 1;";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {

        $array[] = $row;
        
        //$sunclass   = $row["SUN_CLASS"];   
        //$hsl_h      = $row["HSL_H"];
        //$hsl_s      = $row["HSL_S"];
        //$hsl_l      = $row["HSL_L"];
}
} else {
    echo "0 results";
}

echo json_encode($array);

$conn->close();

?>