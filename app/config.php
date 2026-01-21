<?php
/**
 * =============================================================================
 * Database Configuration File
 * =============================================================================
 * This file contains the settings PHP needs to connect to MySQL.
 * 
 * In a real application, these values would come from environment variables
 * for security. For this demo, we use simple constants.
 * 
 * How PHP connects to MySQL:
 * 1. PHP uses the mysqli extension (MySQL Improved)
 * 2. It needs: hostname, username, password, database name
 * 3. These match what we configured in the Dockerfile
 * =============================================================================
 */

// -----------------------------------------------------------------------------
// Database Connection Settings
// -----------------------------------------------------------------------------

/**
 * DB_HOST: Where MySQL is running
 * 'localhost' means MySQL is on the same machine as PHP
 * In our single-container setup, both are in the same container
 */
define('DB_HOST', 'localhost');

/**
 * DB_USER: MySQL username
 * We're using 'root' which is the MySQL administrator account
 * In production, you would create a dedicated user with limited permissions
 */
define('DB_USER', 'root');

/**
 * DB_PASS: MySQL password
 * This matches the password we set in the Dockerfile startup script
 * WARNING: In production, never hardcode passwords!
 */
define('DB_PASS', 'rootpassword');

/**
 * DB_NAME: Which database to connect to
 * This matches the database we created in init.sql
 */
define('DB_NAME', 'lamp_demo');

// -----------------------------------------------------------------------------
// Create Database Connection
// -----------------------------------------------------------------------------

/**
 * Create a connection to MySQL
 * 
 * mysqli_connect() parameters:
 * 1. hostname - where MySQL is running
 * 2. username - MySQL user
 * 3. password - MySQL password  
 * 4. database - which database to use
 * 
 * Returns a connection object if successful, or false if it fails
 */
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

/**
 * Check if connection was successful
 * 
 * mysqli_connect_error() returns the error message if connection failed
 * die() stops the script and displays an error message
 */
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/**
 * Set character encoding to UTF-8
 * 
 * This ensures special characters (like é, ñ, 中文) are handled correctly
 * Always set this after connecting to avoid encoding issues
 */
mysqli_set_charset($conn, "utf8mb4");

// If we reach this point, connection was successful!
// The $conn variable is now available for database queries
?>
