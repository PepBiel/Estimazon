<?php
$con = mysqli_connect("localhost","root","")
    or die("Error localhost");
$db = mysqli_select_db($con,"estimazonfinal")
    or die("Error database");
?>