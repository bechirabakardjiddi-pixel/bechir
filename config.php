<?php
session_start();

$host = 'localhost';
$dbname = 'pharma_turquie';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

function generateToken() {
    return bin2hex(random_bytes(32));
}

function isPartnerLogged() {
    return isset($_SESSION['partner_token']) && isset($_SESSION['partner_id']);
}
?>