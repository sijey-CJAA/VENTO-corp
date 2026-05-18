<?php
$host = 'localhost';
$dbname = 'vento';
$username = 'root';
$password = '';

try {
    // 1. Connect to the MySQL server without selecting the database initially
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Check if a reset is requested via URL parameter '?db_reset=1' or '?db_reset=true'
    $forceReset = isset($_GET['db_reset']) && ($_GET['db_reset'] === '1' || $_GET['db_reset'] === 'true');
    if ($forceReset) {
        $pdo->exec("DROP DATABASE IF EXISTS `$dbname`;");
    }
    
    // 2. Create the database if it doesn't already exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
    
    // 3. Select the database
    $pdo->exec("USE `$dbname`;");
    
    // 4. Verify if schema tables are already present (using the core 'admins' table as a indicator)
    $stmt = $pdo->query("SHOW TABLES LIKE 'admins'");
    if ($stmt->rowCount() === 0) {
        $schemaPath = __DIR__ . '/../database/schema.sql';
        if (file_exists($schemaPath)) {
            $sql = file_get_contents($schemaPath);
            // Execute the multi-query schema to initialize database structure and seeding data
            $pdo->exec($sql);
        } else {
            throw new Exception("Schema SQL file not found at: " . $schemaPath);
        }
        
        // If a reset was forced, redirect to the clean URL (without query parameters) to prevent loop/refresh resets
        if ($forceReset && !headers_sent()) {
            $cleanUrl = strtok($_SERVER['REQUEST_URI'], '?');
            header("Location: $cleanUrl");
            exit;
        }
    }
} catch (Exception $e) {
    die("Database initialization failed: " . $e->getMessage());
}
?>
