<?php
// Vercel Environment Configuration
$db_host = getenv('DB_HOST') ?: '';
$db_port = getenv('DB_PORT') ?: '3306';
$db_user = getenv('DB_USER') ?: '';
$db_pass = getenv('DB_PASS') ?: '';
$db_name = getenv('DB_NAME') ?: '';
$db_prefix = getenv('DB_PREFIX') ?: 'pp_';

// Bypass standard GUI installation since Vercel is read-only
$requriemntnoneedchecked = true;
?>
