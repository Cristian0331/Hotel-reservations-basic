<?php
$host = '127.0.0.1';
$port = '5432';
$user = 'postgres';
$pass = '1076904350';
$dbname = 'hotel_reservations';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT 1 FROM pg_database WHERE datname = '$dbname'");
    if ($stmt->fetchColumn()) {
        echo "Database '$dbname' already exists.\n";
    } else {
        $pdo->exec("CREATE DATABASE \"$dbname\"");
        echo "Database '$dbname' created successfully.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
