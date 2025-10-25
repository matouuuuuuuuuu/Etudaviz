<?php
// fichier: include/db_connect.php

$host = 'localhost';
$dbname = 'etudaviz';
$username = 'root';
$password = ''; // à adapter

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
