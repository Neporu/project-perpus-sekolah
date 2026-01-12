<?php

$localhost	= "localhost";
$username	= "root";
$password	= "";
$dbname		= "kel1perpus_sekolah";

$con = new mysqli($localhost, $username, $password, $dbname);

if($con->connect_error) {
	die("Gagal Koneksi : " . $conn->connect_error);
}
