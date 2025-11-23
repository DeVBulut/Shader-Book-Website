<?php
declare(strict_types=1);

// Simple SQLite database setup for authentication examples.
// Stored locally in data/app.sqlite so the demo works without external services.
$dataDir = __DIR__ . DIRECTORY_SEPARATOR . 'data';
if (!is_dir($dataDir)) @mkdir($dataDir, 0775, true);
$dbFile = $dataDir . DIRECTORY_SEPARATOR . 'app.sqlite';

/**
 * Lightweight PDO-ish wrapper around SQLite3 for environments that lack the PDO SQLite driver.
 * Only implements the subset of methods this project uses (prepare, exec, lastInsertId).
 */
class PdoStmtCompat
{
    private SQLite3Stmt $stmt;
    private ?SQLite3Result $result = null;

    public function __construct(SQLite3 $db, string $sql)
    {
        $this->stmt = $db->prepare($sql);
    }

    public function execute(array $params = []): bool
    {
        // Bind named parameters (e.g., :e) if provided.
        foreach ($params as $key => $value) {
            $name = ltrim($key, ':');
            $type = is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT;
            $this->stmt->bindValue(':' . $name, $value, $type);
        }
        $this->result = $this->stmt->execute();
        return $this->result !== false;
    }

    public function fetch(): array|false
    {
        if ($this->result === null) return false;
        $row = $this->result->fetchArray(SQLITE3_ASSOC);
        return $row === false ? false : $row;
    }
}

class PdoCompat
{
    private SQLite3 $db;

    public function __construct(string $file)
    {
        $this->db = new SQLite3($file);
    }

    public function prepare(string $sql): PdoStmtCompat
    {
        return new PdoStmtCompat($this->db, $sql);
    }

    public function exec(string $sql): bool
    {
        return $this->db->exec($sql);
    }

    public function lastInsertId(): int
    {
        return $this->db->lastInsertRowID();
    }
}

// Prefer PDO SQLite; fall back to SQLite3 if the PDO driver is missing.
if (extension_loaded('pdo_sqlite')) {
    $pdo = new PDO('sqlite:' . $dbFile, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} elseif (extension_loaded('sqlite3') && class_exists('SQLite3')) {
    // SQLite3 is bundled with PHP by default, so this path keeps the demo working without extra installs.
    $pdo = new PdoCompat($dbFile);
} else {
    // Neither SQLite extension is available
    die('
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Database Error</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
            h1 { color: #d32f2f; }
            code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; }
        </style>
    </head>
    <body>
        <h1>Database Extension Not Found</h1>
        <p>This application requires either the <code>pdo_sqlite</code> or <code>sqlite3</code> PHP extension to be enabled.</p>
        <h2>How to Fix:</h2>
        <ol>
            <li>Open your PHP configuration file (<code>php.ini</code>)</li>
            <li>Find and uncomment (remove the semicolon) one of these lines:
                <ul>
                    <li><code>extension=pdo_sqlite</code> (preferred)</li>
                    <li><code>extension=sqlite3</code></li>
                </ul>
            </li>
            <li>Restart your web server</li>
        </ol>
        <p><strong>Note:</strong> The location of <code>php.ini</code> can be found by running <code>php --ini</code> in your terminal or by checking <code>phpinfo()</code>.</p>
    </body>
    </html>');
}

// Ensure required tables exist.
$pdo->exec('CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  first_name TEXT NOT NULL,
  last_name TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  favorite_chapter TEXT NULL
)');
