<?php

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

$sql = "SELECT system_positions.SYSTEM_ID, system_positions.Position_X, system_positions.Position_Y, system_positions.Position_Z, systems.SUN_ID, systems.System_Name, suns.SUN_CLASS, suns.Sun_Name, suns.Sun_Temp, suns.Sun_Mass, suns.Sun_Radius, suns.Sun_Luminosity, sun_classes.Chromaticity_hex 
FROM system_positions 
JOIN systems ON systems.SYSTEM_ID = system_positions.SYSTEM_ID 
JOIN suns ON systems.SUN_ID = suns.SUN_ID
JOIN sun_classes ON suns.SUN_CLASS = sun_classes.SUN_CLASS
LIMIT 800;";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {

        $array[] = $row;
        
        //$sysid      = $row["SYSTEM_ID"];
        $position_x = $row["Position_X"];
        $position_y = $row["Position_Y"];
        $position_z = $row["Position_Z"];
        //$sunid      = $row["SUN_ID"];
        //$sysname    = $row["System_Name"];
        //$planetid   = $row["PLANET_ID"];
        $sunclass   = $row["SUN_CLASS"];
        //$sunname    = $row["Sun_Name"];
        //$suntemp    = $row["Sun_Temp"];
        //$sunmass    = $row["Sun_Mass"];
        $sunradius  = $row["Sun_Radius"];
        //$sunlumi    = $row["Sun_Luminosity"];
        $sunhex     = $row["Chromaticity_hex"];
}
} else {
    echo "0 results";
}

$data = array(
    //'sysid'     => $sysid, 
    'position_x'  => $position_x,
    'position_y'  => $position_y,
    'position_z'  => $position_z, 
    //'sunid'     => $sunid, 
    //'sysname'   => $sysname,
    //'planetid'  => $planetid, 
    'sunclass'  => $sunclass, 
    //'sunname'   => $sunname, 
    //'suntemp'   => $suntemp, 
    //'sunmass'   => $sunmass, 
    'sunradius' => $sunradius, 
    //'sunlumi'   => $sunlumi, 
    'sunhex'    => $sunhex);

echo json_encode($array);



$conn->close();

?>