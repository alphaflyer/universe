<!DOCTYPE html>
<html>
<head>
<title>STAR VIS</title>

<link href="https://fonts.googleapis.com/css?family=Orbitron:400,700" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="lib/style/style.css">

</head>
<body>

<h1>STAR VISUALISATION</h1>
<p></p>

<table style='width: 40%'>
    <tr>
        <td>RND</td>
        <td>A</td>
        <td>B</td>
        <td>F</td>
        <td>G</td>
        <td>K</td>
        <td>M</td>
        <td>O</td>
    </tr>
</table>

<p></p>

<table>

<?php

$viscount = 0;
//$class = $_GET['class'];
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
// echo "Connected successfully<br><br>";

$sql = "SELECT
            *
            FROM
            suns
            JOIN sun_classes ON sun_classes.SUN_CLASS = suns.SUN_CLASS
            ORDER BY RAND() 
            LIMIT 25;";

                //WHERE sun_classes.SUN_CLASS = '$class'
                
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        
        $halo = 1*$row["Sun_Luminosity"];
        if ($halo < 1) {
            $halo = 1;
        } else {
            $halo = 1*$row["Sun_Luminosity"];
        }

        $lumi = 1;
        if ($lumi*$row["Sun_Luminosity"] >= 1) {
            $lumi = 1;
        } elseif ($lumi*$row["Sun_Luminosity"] <= 0.5) {
            $lumi = 0.5;
        } else {
            $lumi = $lumi*($row["Sun_Luminosity"]);
        }
        
        echo "
            <tr>
                    <th>Star name</th>
                    <th>ID</th>
                    <th>Class</th>
                    <th>Temp</th>
                    <th>Mass</th>
                    <th>Radius</th>
                    <th>Luminosity</th> 
                    <th>Color</th>
                    <th>Color hex</th>
                    <th>Star vis</th>
                </tr>
            <tr>";
        
        echo "<td>" . $row["Sun_Name"] . "</td>" . 
                "<td>" . $row["SUN_ID"] . "</td>" . 
                "<td>" . $row["SUN_CLASS"] . "</td>" . 
                "<td>" . $row["Sun_Temp"] . "</td>" . 
                "<td>" . $row["Sun_Mass"] . "</td>" . 
                "<td>" . $row["Sun_Radius"] . "</td>" . 
                "<td>" . $row["Sun_Luminosity"] . "</td>" . 
                "<td>" . $row["Chromaticity"] . "</td>" . 
                "<td>" . $row["Chromaticity_hex"] . "</td>
                
                 <td>

                    <svg height='400' width='400'>
                    <defs>
                    <radialGradient id='grad"
                    
                        . $viscount . 
                    
                        "'cx='50%' cy='50%' r='40%' fx='50%' fy='50%'>
                        <stop offset='40%' style='stop-color:"
                        
                        . $row["Chromaticity_hex"] .
                        
                        ";stop-opacity:" 
                        
                        . (0.7+$row["Sun_Luminosity"]) . 

                        "' />
                        <stop offset='100%' style='stop-color:"
                        
                        . $row["Chromaticity_hex"] . 
                        
                        ";stop-opacity:0' />
                    </radialGradient>
                    </defs>
                    <ellipse cx='200' cy='200' rx='" . ((100*$row["Sun_Radius"])*($halo+1)) . "' ry='" . ((100*$row["Sun_Radius"])*($halo+1)) . "' fill='url(#grad"
                    
                        . $viscount . 
                    
                    ")' />
                    <defs>
                    <radialGradient id='grada"
                    
                        . $viscount . 
                    
                        "'cx='50%' cy='50%' r='50%' fx='50%' fy='50%'>
                        <stop offset='50%' style='stop-color: white;stop-opacity:" . $lumi . "' />
                        <stop offset='100%' style='stop-color:"
                        
                        . $row["Chromaticity_hex"] . 
                        
                        ";stop-opacity:1' />
                    </radialGradient>
                    </defs>
                    <ellipse cx='200' cy='200' rx='" . 100*$row["Sun_Radius"] . "' ry='" . 100*$row["Sun_Radius"] . "' fill='url(#grada"
                    
                        . $viscount . 
                    
                    ")' />
                    </svg>
                 </td>
                </tr>
        
        ";

        $viscount++;

        

}
} else {
    echo "0 results";
}

$conn->close();

?>

</table>

</body>
</html>