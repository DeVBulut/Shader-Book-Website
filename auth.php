<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function current_user($pdo): ?array {
    if (!isset($_SESSION['user_id'])) return null;
    $q = $pdo->prepare('SELECT id, first_name, last_name, email, favorite_chapter FROM users WHERE id = :id');
    $q->execute([':id' => (int)$_SESSION['user_id']]);
    $u = $q->fetch();
    return $u ?: null;
}

function require_login_redirect(string $to = 'login.php'): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . $to);
        exit;
    }
}

