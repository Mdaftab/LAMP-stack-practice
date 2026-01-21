<?php
/**
 * =============================================================================
 * LAMP Stack Demo Application
 * =============================================================================
 * This PHP application demonstrates a complete LAMP stack integration:
 * - Linux: The operating system (Ubuntu in our Docker container)
 * - Apache: The web server delivering this page to your browser
 * - MySQL: The database storing and retrieving data
 * - PHP: This script processing the logic
 * 
 * Features:
 * - Add new users to the database (INSERT operation)
 * - Display all users from the database (SELECT operation)
 * - Delete users (DELETE operation)
 * - Shows real-time database connectivity
 * =============================================================================
 */

// Include database configuration and connection
require_once 'config.php';

// -----------------------------------------------------------------------------
// Handle Form Submissions
// -----------------------------------------------------------------------------

$message = '';  // Feedback message to display to user
$messageType = '';  // 'success' or 'error' for styling

// Check if a form was submitted (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Handle "Add User" form submission
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        /**
         * Get form data and sanitize it
         * 
         * mysqli_real_escape_string() prevents SQL injection attacks
         * by escaping special characters that could break the SQL query
         * 
         * trim() removes whitespace from beginning and end
         */
        $name = mysqli_real_escape_string($conn, trim($_POST['name']));
        $email = mysqli_real_escape_string($conn, trim($_POST['email']));
        
        // Validate that both fields have values
        if (!empty($name) && !empty($email)) {
            /**
             * INSERT query to add new user
             * 
             * SQL INSERT syntax:
             * INSERT INTO table_name (column1, column2) VALUES (value1, value2)
             */
            $sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";
            
            // Execute the query
            if (mysqli_query($conn, $sql)) {
                $message = "User '$name' added successfully!";
                $messageType = 'success';
            } else {
                $message = "Error adding user: " . mysqli_error($conn);
                $messageType = 'error';
            }
        } else {
            $message = "Please fill in both name and email fields.";
            $messageType = 'error';
        }
    }
    
    // Handle "Delete User" form submission
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = intval($_POST['id']);  // Convert to integer for safety
        
        /**
         * DELETE query to remove a user
         * 
         * SQL DELETE syntax:
         * DELETE FROM table_name WHERE condition
         */
        $sql = "DELETE FROM users WHERE id = $id";
        
        if (mysqli_query($conn, $sql)) {
            $message = "User deleted successfully!";
            $messageType = 'success';
        } else {
            $message = "Error deleting user: " . mysqli_error($conn);
            $messageType = 'error';
        }
    }
}

// -----------------------------------------------------------------------------
// Fetch All Users from Database
// -----------------------------------------------------------------------------

/**
 * SELECT query to retrieve all users
 * 
 * SQL SELECT syntax:
 * SELECT column1, column2 FROM table_name ORDER BY column
 * 
 * * means "all columns"
 * ORDER BY created_at DESC sorts newest first
 */
$sql = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

// Get server information for the info panel
$phpVersion = phpversion();
$mysqlVersion = mysqli_get_server_info($conn);
$apacheVersion = $_SERVER['SERVER_SOFTWARE'] ?? 'Apache';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAMP Stack Demo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <header>
            <h1>LAMP Stack Demo</h1>
            <p class="subtitle">Linux + Apache + MySQL + PHP working together</p>
        </header>

        <!-- System Information Panel -->
        <section class="info-panel">
            <h2>Stack Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="label">Linux</span>
                    <span class="value"><?php echo php_uname('s') . ' ' . php_uname('r'); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Apache</span>
                    <span class="value"><?php echo $apacheVersion; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">MySQL</span>
                    <span class="value"><?php echo $mysqlVersion; ?></span>
                </div>
                <div class="info-item">
                    <span class="label">PHP</span>
                    <span class="value"><?php echo $phpVersion; ?></span>
                </div>
            </div>
            <div class="connection-status success">
                ✓ Database Connected Successfully
            </div>
        </section>

        <!-- Feedback Message -->
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Add User Form -->
        <section class="form-section">
            <h2>Add New User</h2>
            <p class="description">
                This form demonstrates the <strong>INSERT</strong> operation - adding data to MySQL.
            </p>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required 
                           placeholder="Enter user name">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="Enter email address">
                </div>
                <button type="submit" class="btn btn-primary">Add User</button>
            </form>
        </section>

        <!-- Users List -->
        <section class="users-section">
            <h2>Users in Database</h2>
            <p class="description">
                This table demonstrates the <strong>SELECT</strong> operation - retrieving data from MySQL.
            </p>
            
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" 
                                               value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-small"
                                                onclick="return confirm('Delete this user?');">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No users found in the database.</p>
            <?php endif; ?>
        </section>

        <!-- Data Flow Explanation -->
        <section class="explanation">
            <h2>How This Works</h2>
            <div class="flow-diagram">
                <div class="flow-step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <strong>Browser Request</strong>
                        <p>You visit this page in your browser</p>
                    </div>
                </div>
                <div class="flow-arrow">→</div>
                <div class="flow-step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <strong>Apache Receives</strong>
                        <p>Apache web server receives the HTTP request</p>
                    </div>
                </div>
                <div class="flow-arrow">→</div>
                <div class="flow-step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <strong>PHP Processes</strong>
                        <p>PHP executes this script and queries MySQL</p>
                    </div>
                </div>
                <div class="flow-arrow">→</div>
                <div class="flow-step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <strong>MySQL Returns Data</strong>
                        <p>Database returns requested information</p>
                    </div>
                </div>
                <div class="flow-arrow">→</div>
                <div class="flow-step">
                    <div class="step-number">5</div>
                    <div class="step-content">
                        <strong>HTML Response</strong>
                        <p>PHP generates HTML, Apache sends to browser</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <p>LAMP Stack sameer lab Demo - For Learning Purposes</p>
            <p>
                <small>
                    Page generated in <?php echo round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 4); ?> seconds
                </small>
            </p>
        </footer>
    </div>
</body>
</html>

<?php
// Close the database connection when done
mysqli_close($conn);
?>
