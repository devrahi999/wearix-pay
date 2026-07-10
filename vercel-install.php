<?php
date_default_timezone_set('UTC');

require __DIR__ . '/pp-config.php';
require __DIR__ . '/pp-content/pp-include/pp-functions.php';

echo "<h2>Starting Vercel Database Setup...</h2>";

if (empty($db_host) || empty($db_user) || empty($db_name)) {
    die("Error: Database Environment Variables (DB_HOST, DB_USER, DB_NAME) are not set in Vercel.");
}

try {
    $pdo = connectDatabase();
    
    // Import DB
    $sqlContent = file_get_contents(__DIR__ . '/pp-content/pp-install/db.sql');
    $sqlContent = str_replace('pp_', $db_prefix, $sqlContent);
    $queries = array_filter(array_map('trim', explode(";\n", $sqlContent)));
    
    $pdo->beginTransaction();
    foreach ($queries as $query) {
        if ($query !== '') {
            $pdo->exec($query);
        }
    }
    if ($pdo->inTransaction()) {
        $pdo->commit();
    }
    
    echo "Database tables imported successfully.<br>";

    // Admin Details
    $a_id = substr(str_shuffle("0123456789"), 0, 7);
    $brand_id = substr(str_shuffle("0123456789"), 0, 7);
    $adminName = 'Super Admin';
    $adminUsername = 'admin';
    $adminEmail = 'admin@admin.com';
    $adminPass = '12345678';
    $hashedPass = password_hash($adminPass, PASSWORD_DEFAULT);
    
    $columns = ['a_id', 'full_name', 'username', 'email', 'password', 'created_date', 'updated_date'];
    $values = [$a_id, $adminName, $adminUsername, $adminEmail, $hashedPass, getCurrentDatetime('Y-m-d H:i:s'), getCurrentDatetime('Y-m-d H:i:s')];
    insertData($db_prefix.'admin', $columns, $values);

    $columns = ['brand_id', 'a_id', 'permission', 'created_date', 'updated_date'];
    $values = [$brand_id, $a_id, json_encode(permissionSchema()), getCurrentDatetime('Y-m-d H:i:s'), getCurrentDatetime('Y-m-d H:i:s')];
    insertData($db_prefix.'permission', $columns, $values);

    $columns = ['brand_id', 'created_date', 'updated_date'];
    $values = [$brand_id, getCurrentDatetime('Y-m-d H:i:s'), getCurrentDatetime('Y-m-d H:i:s')];
    insertData($db_prefix.'brands', $columns, $values);

    $columns = ['brand_id', 'code', 'symbol', 'created_date', 'updated_date'];
    $values = [$brand_id, 'BDT', '৳', getCurrentDatetime('Y-m-d H:i:s'), getCurrentDatetime('Y-m-d H:i:s')];
    insertData($db_prefix.'currency', $columns, $values);

    echo "<h3>Setup Complete!</h3>";
    echo "<b>Admin Username:</b> $adminUsername<br>";
    echo "<b>Admin Password:</b> $adminPass<br>";
    echo "<b>Login URL:</b> <a href='/login'>/login</a><br><br>";
    echo "<p style='color:red;'><b>IMPORTANT:</b> Please delete this file (`vercel-install.php`) from your repository after setup for security!</p>";

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "<b style='color:red;'>Error:</b> " . $e->getMessage();
}
?>
