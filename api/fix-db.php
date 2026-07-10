<?php
require __DIR__ . '/../pp-config.php';

try {
    $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$db_name'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<h2>Database Fix Script</h2>";

    foreach ($tables as $table) {
        $colStmt = $pdo->query("SHOW COLUMNS FROM `$table` WHERE Field = 'id'");
        $col = $colStmt->fetch(PDO::FETCH_ASSOC);

        if ($col) {
            if (strpos(strtolower($col['Extra']), 'auto_increment') === false) {
                try {
                    $pdo->exec("ALTER TABLE `$table` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT");
                    echo "<p style='color: green;'>Modified table `$table` to add AUTO_INCREMENT.</p>";
                } catch (Exception $e) {
                    echo "<p style='color: red;'>Error modifying table `$table`: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p style='color: gray;'>Table `$table` already has AUTO_INCREMENT.</p>";
            }
        }
    }
    
    echo "<h3>Done fixing database!</h3>";
    echo "<p>You can now delete this file from the codebase.</p>";
} catch (Exception $e) {
    echo "<h3>Database connection error:</h3> " . $e->getMessage();
}
