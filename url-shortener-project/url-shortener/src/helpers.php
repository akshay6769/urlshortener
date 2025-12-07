<?php
// src/helpers.php

require_once __DIR__ . '/db.php';

// Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Return the current authenticated user record, or null if none.
 *
 * @return array|null
 */
function current_user()
{
    if (!empty($_SESSION['user_id'])) {
        $pdo  = db();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);

        return $stmt->fetch();
    }

    return null;
}

/**
 * Log in a user by their database ID.
 *
 * @param int $id
 */
function login_user_by_id($id)
{
    $_SESSION['user_id'] = $id;
}

/**
 * Log out the current user and destroy the session.
 */
function logout_user()
{
    session_unset();
    session_destroy();
}

/**
 * Require user authentication.
 * Redirects to login if not authenticated.
 */
function require_auth()
{
    if (!current_user()) {
        header('Location: /login');
        exit;
    }
}

/**
 * Require that the user has at least one of the given roles.
 *
 * @param array $roles
 */
function require_role(array $roles)
{
    $user = current_user();

    if (!$user || !in_array($user['role'], $roles)) {
        http_response_code(403);
        echo "<h3>403 Forbidden</h3>";
        exit;
    }
}

/**
 * Generate a unique random short code.
 * Ensures it does not exist in the short_urls table.
 *
 * @param int $length
 * @return string
 */
function make_code($length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $maxIndex   = strlen($characters) - 1;

    $code = '';

    // Build random code
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[random_int(0, $maxIndex)];
    }

    // Ensure uniqueness
    $pdo  = db();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM short_urls WHERE code = ?");
    $stmt->execute([$code]);

    if ($stmt->fetchColumn() > 0) {
        return make_code($length); // regenerate if collision
    }

    return $code;
}

/**
 * Sanitize and validate a URL.
 * Automatically prefixes https:// if missing.
 *
 * @param string $url
 * @return string|false
 */
function sanitize_url($url)
{
    $url = trim($url);

    if ($url === '') {
        return false;
    }

    // Add scheme if missing
    if (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }

    return filter_var($url, FILTER_VALIDATE_URL) ? $url : false;
}
