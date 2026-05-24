#!/usr/bin/env php
<?php
/**
 * Script setup test database
 * Chạy: php setup-test-db.php
 */

$env = 'testing';
echo "🚀 Setting up test database ({$env})...\n\n";

// 1. Tạo database ảo
echo "1️⃣  Creating test database 'dethitracnghiem_test'...\n";
$result = shell_exec("cd " . __DIR__ . " && php artisan db:create --database=dethitracnghiem_test 2>&1");
echo $result ?? "✅ Database created or already exists\n";

// 2. Chạy migrations
echo "\n2️⃣  Running migrations...\n";
$result = shell_exec("cd " . __DIR__ . " && php artisan migrate:fresh --env={$env} 2>&1");
echo $result;

// 3. Chạy seeders
echo "\n3️⃣  Seeding test data...\n";
$result = shell_exec("cd " . __DIR__ . " && php artisan db:seed --env={$env} 2>&1");
echo $result;

echo "\n✅ Test database setup complete!\n";
echo "📝 Test database: dethitracnghiem_test\n";
echo "🧪 Run tests with: php artisan test --env=testing\n";
?>