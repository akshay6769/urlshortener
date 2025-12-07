<?php

// -------------------------------------------------------
// Debug settings (development only)
// -------------------------------------------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// -------------------------------------------------------
// Bootstrap helpers
// -------------------------------------------------------
require_once __DIR__ . '/src/helpers.php';


// -------------------------------------------------------
// Normalize request path
// -------------------------------------------------------
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


// -------------------------------------------------------
// AUTH ROUTES
// -------------------------------------------------------

// Dashboard (default)
if ($path === '/' || $path === '/dashboard') {
    require_once __DIR__ . '/src/controllers/DashboardController.php';
    (new DashboardController())->index();
    exit;
}

// Login form
if ($path === '/login' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once __DIR__ . '/src/controllers/AuthController.php';
    (new AuthController())->loginView();
    exit;
}

// Login submit
if ($path === '/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/src/controllers/AuthController.php';
    (new AuthController())->login();
    exit;
}

// Registration is disabled — but route exists for UI
if ($path === '/register' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once __DIR__ . '/src/controllers/AuthController.php';
    (new AuthController())->registerView();
    exit;
}

// Block registration POST
if ($path === '/register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/src/controllers/AuthController.php';
    (new AuthController())->register();
    exit;
}

// Logout
if ($path === '/logout') {
    require_once __DIR__ . '/src/controllers/AuthController.php';
    (new AuthController())->logout();
    exit;
}



// -------------------------------------------------------
// SHORT URL ROUTES
// -------------------------------------------------------

// Show create form
if ($path === '/short/create') {
    require_once __DIR__ . '/src/controllers/ShortUrlController.php';
    (new ShortUrlController())->create();
    exit;
}

// Store new short URL
if ($path === '/short/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/src/controllers/ShortUrlController.php';
    (new ShortUrlController())->store();
    exit;
}

// Download URLs as CSV
if ($path === '/short/download') {
    require_once __DIR__ . '/src/controllers/ShortUrlController.php';
    (new ShortUrlController())->downloadCSV();
    exit;
}



// -------------------------------------------------------
// INVITATION ROUTES
// -------------------------------------------------------

// Invite form
if ($path === '/invite/send' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once __DIR__ . '/src/controllers/InvitationController.php';
    (new InvitationController())->sendView();
    exit;
}

// Handle invite post
if ($path === '/invite/send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/src/controllers/InvitationController.php';
    (new InvitationController())->send();
    exit;
}

// Accept invitation
if (strpos($path, '/invite/accept') === 0) {
    require_once __DIR__ . '/src/controllers/InvitationController.php';
    (new InvitationController())->accept();
    exit;
}



// -------------------------------------------------------
// TEAM ROUTE
// -------------------------------------------------------

// Team listing page
if ($path === '/team') {
    require_once __DIR__ . '/src/controllers/TeamController.php';
    (new TeamController())->index();
    exit;
}



// -------------------------------------------------------
// SHORT URL RESOLVER  (MUST ALWAYS BE LAST)
// -------------------------------------------------------
$possible = ltrim($path, '/');

if ($possible !== '' && !str_starts_with($possible, 'api')) {
    require_once __DIR__ . '/src/controllers/ShortUrlController.php';
    (new ShortUrlController())->resolve($possible);
    exit;
}



// -------------------------------------------------------
// FALLBACK 404 ERROR
// -------------------------------------------------------
http_response_code(404);
echo "404 Not Found";
