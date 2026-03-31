<?php
    $host = "localhost";
    $user = "root";                     
    $pass = "";                                  
    $db = "movietheatredb";
    $con = mysqli_connect($host, $user, $pass, $db);

	if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
    //$port = 3306;
     //$con = mysqli_connect($host, $user, $pass, $db, $port)or die(mysqli_connect_error());
?>