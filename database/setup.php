<?php

// تأكد من تضمين autoload إذا كنت تستخدم Composer
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $pdo = new PDO("sqlite:" . __DIR__ . '/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        user_id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL,
        email TEXT NOT NULL,
        password TEXT NOT NULL,
        role TEXT,
        created_at TEXT,
        updated_at TEXT,
        last_login TEXT
    )");

    echo "Database and table 'users' created successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
