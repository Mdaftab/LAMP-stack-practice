-- =============================================================================
-- Database Initialization Script
-- =============================================================================
-- This script runs when the container starts for the first time.
-- It creates our demo database and tables.
--
-- Understanding the SQL commands:
-- - CREATE DATABASE: Creates a new database (like creating a new folder)
-- - USE: Selects which database to work with
-- - CREATE TABLE: Creates a table to store data (like a spreadsheet)
-- - INSERT: Adds new rows of data to a table
-- =============================================================================

-- -----------------------------------------------------------------------------
-- Step 1: Create the Database
-- -----------------------------------------------------------------------------
-- IF NOT EXISTS: Only create if it doesn't already exist (prevents errors)
CREATE DATABASE IF NOT EXISTS lamp_demo;

-- Switch to using our new database
USE lamp_demo;

-- -----------------------------------------------------------------------------
-- Step 2: Create the Users Table
-- -----------------------------------------------------------------------------
-- This table will store user information for our demo application
CREATE TABLE IF NOT EXISTS users (
    -- id: Unique identifier for each user
    -- INT: Integer (whole number)
    -- AUTO_INCREMENT: MySQL automatically assigns the next number
    -- PRIMARY KEY: This column uniquely identifies each row
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- name: User's name
    -- VARCHAR(100): Text up to 100 characters
    -- NOT NULL: This field is required (cannot be empty)
    name VARCHAR(100) NOT NULL,
    
    -- email: User's email address
    -- VARCHAR(150): Text up to 150 characters
    -- NOT NULL: This field is required
    email VARCHAR(150) NOT NULL,
    
    -- created_at: When this record was created
    -- TIMESTAMP: Date and time value
    -- DEFAULT CURRENT_TIMESTAMP: Automatically set to current time
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------------------------------------
-- Step 3: Insert Sample Data
-- -----------------------------------------------------------------------------
-- Let's add some demo users so the app isn't empty when first loaded
INSERT INTO users (name, email) VALUES 
    ('John Doe', 'john.doe@example.com'),
    ('Jane Smith', 'jane.smith@example.com'),
    ('Demo User', 'demo@lamp-demo.local');

-- -----------------------------------------------------------------------------
-- Verification Query (for debugging)
-- -----------------------------------------------------------------------------
-- This shows what was created - useful for troubleshooting
SELECT 'Database initialized successfully!' AS status;
SELECT COUNT(*) AS total_users FROM users;
