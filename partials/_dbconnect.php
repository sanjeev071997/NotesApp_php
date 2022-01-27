<?php

$server = "localhost";
$username = "root";
$password = "";
$database = "notesapp";

$conn = mysqli_connect($server, $username, $password, $database);

if (!$conn) {
    die("Error". mysqli_connect_error());
}

// if ($conn) {
//     echo "success ";
// }
// else{
//     die ("Error" . mysqli_connect_error());
// }

?>