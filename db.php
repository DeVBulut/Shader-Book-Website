<?php
declare(strict_types=1);

// Simple SQLite database setup for authentication examples.
// Stored locally in data/app.sqlite so the demo works without external services.
$dataDir = __DIR__ . DIRECTORY_SEPARATOR . 'data';
if (!is_dir($dataDir)) @mkdir($dataDir, 0775, true);
$dbFile = $dataDir . DIRECTORY_SEPARATOR . 'app.sqlite';

$pdo = new PDO('sqlite:' . $dbFile, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// Ensure required tables exist.
$pdo->exec('CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  first_name TEXT NOT NULL,
  last_name TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  favorite_chapter TEXT NULL
)');

