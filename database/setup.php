<?php
/**
 * Database Setup Script
 * 
 * This script creates the database and runs the schema SQL file.
 */

// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect to MySQL server
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to MySQL server successfully.\n";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS food_marketplace");
    echo "Database 'food_marketplace' created or already exists.\n";
    
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=food_marketplace", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute the schema SQL file
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    
    // Split SQL by semicolon to execute multiple statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "Database schema created successfully.\n";
    echo "FoodieHub database setup completed!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
