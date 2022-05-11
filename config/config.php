<?php
ob_start(); // Turns on output buffering
session_start(); // allow as to store the values on sessions

//$timezone = date_default_timezone_set()

$con = mysqli_connect("localhost", 'root', '', 'zychat');

if (mysqli_connect_errno()){
    echo "Failed to connect: " . mysqli_connect_error();
}