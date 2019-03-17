<?php
ob_start();
$host = 'HOST';
$username = 'USERNAME';
$password = 'PASSWORD';
$dbname = 'DBNAME';

try {
    $connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}