<?php
$host = 'tramway.proxy.rlwy.net'; // ejemplo: containers-us-west-53.railway.app
$port = '48471';
$dbname = 'railway';
$user = 'postgres';
$password = 'mWcjgQlrbvjHdJhwMYjVQOMrSEdVIBfE';

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>