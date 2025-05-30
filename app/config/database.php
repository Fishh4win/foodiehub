<?php
/**
 * Database Configuration
 * 
 * This file contains the database connection settings for the FoodieHub application.
 */

return [
    'host' => 'localhost',
    'dbname' => 'food_marketplace',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
