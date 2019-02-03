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

$sql = "SELECT  system_positions.SYSTEM_ID, 
                system_positions.Position_X, 
                system_positions.Position_Y, 
                system_positions.Position_Z,                 
                systems.System_Name, 
                suns.SUN_CLASS,                 
                suns.Sun_Radius,
                suns.Sun_Luminosity
FROM system_positions 
JOIN systems ON systems.SYSTEM_ID = system_positions.SYSTEM_ID 
JOIN suns ON systems.SUN_ID = suns.SUN_ID
JOIN sun_classes ON suns.SUN_CLASS = sun_classes.SUN_CLASS
LIMIT 100000;";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {

        $array[] = $row;
        
        $sysid      = $row["SYSTEM_ID"];
        $position_x = $row["Position_X"];
        $position_y = $row["Position_Y"];
        $position_z = $row["Position_Z"];
        $luminosity = $row["Sun_Luminosity"];
        $sysname    = $row["System_Name"];   
        $sunclass   = $row["SUN_CLASS"];       
        $sunradius  = $row["Sun_Radius"];  
}
} else {
    echo "0 results";
}

echo json_encode($array);

$conn->close();

?>