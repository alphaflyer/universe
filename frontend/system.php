<!DOCTYPE html>
<html>
<head>
<title>System visualisation</title>
</head>
<body>

<h1>System visualisation</h1>
<p></p>

<?php
$SYS_ID = "1";
$servername = "localhost";
$username = "universe";
$password = "F7V4Z50Jl7HyKFI8";
$dbname = "universe";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully<br><br>";

$sql = "SELECT
            systems.SYSTEM_ID,
            suns.Sun_Name,
            sun_classes.Sun_Class,
            sun_classes.Sun_Class_Name,
            suns.Sun_Size,
            planets.Planet_Name,
            systems.Planet_Rotation,
            planets.Planet_Size,
            planet_classes.Planet_Class,
            planet_classes.Planet_Class_Name,
            systems.Planet_Orbit,
            systems.Planet_Orbital_Period
        FROM
            systems
        JOIN planets ON systems.PLANET_ID = planets.PLANET_ID
        JOIN planet_classes ON planets.PLANET_CLASS_ID = planet_classes.PLANET_CLASS_ID
        JOIN suns ON systems.SUN_ID = suns.SUN_ID
        JOIN sun_classes ON suns.SUN_CLASSES_ID=sun_classes.SUN_CLASSES_ID
        WHERE
            systems.SYSTEM_ID = $SYS_ID;";

$result = $conn->query($sql);


if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo $row["Planet_Name"] . "  " . $row["Planet_Class"] . "<br>";
    }
} else {
    echo "0 results";
}


$conn->close();






?>

</body>
</html>



