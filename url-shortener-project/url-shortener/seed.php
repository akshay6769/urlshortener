<?php
// seed.php — CLI database initializer

require_once __DIR__ . '/src/db.php';

// Load DB config
$config = require __DIR__ . '/src/config.php';

echo "Seeding database in {$config['DB_DATABASE']}...\n";

$pdo = db();

/* ---------------------------------------------------------
 *  CREATE DEFAULT COMPANY (OPTIONAL)
 * --------------------------------------------------------- */
echo "Create default company? (y/n) [y]: ";
$input = trim(fgets(STDIN));

if ($input === '' || strtolower($input) === 'y') {

    $defaultName = 'Default Company';
    $defaultSlug = 'default-company';

    $stmt = $pdo->prepare("
        INSERT IGNORE INTO companies (name, slug, created_at)
        VALUES (?, ?, NOW())
    ");

    $stmt->execute([$defaultName, $defaultSlug]);

    $companyId = $pdo->lastInsertId();
    echo "Default company ensured. ID: {$companyId}\n";

} else {

    echo "Enter company ID to assign to superadmin (leave empty for none): ";
    $companyId = trim(fgets(STDIN));

    if ($companyId === '') {
        $companyId = null;
    }
}


/* ---------------------------------------------------------
 *  CREATE SUPERADMIN USER
 * --------------------------------------------------------- */
echo "Create superadmin user now.\n";

// Email input
echo "Email for superadmin [super@local.test]: ";
$email = trim(fgets(STDIN));
if ($email === '') {
    $email = 'super@local.test';
}

// Name input
echo "Name [Super Admin]: ";
$name = trim(fgets(STDIN));
if ($name === '') {
    $name = 'Super Admin';
}

// Password input
echo "Password [password]: ";
$password = trim(fgets(STDIN));
if ($password === '') {
    $password = 'password';
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert superadmin into DB
$insert = $pdo->prepare("
    INSERT INTO users (name, email, password, company_id, role, created_at)
    VALUES (?, ?, ?, ?, 'superadmin', NOW())
");

$insert->execute([
    $name,
    $email,
    $hashedPassword,
    $companyId
]);

echo "SuperAdmin created successfully.\n";
echo "Email: {$email}\n";
echo "You may now log in using this account.\n";
