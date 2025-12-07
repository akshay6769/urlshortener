<?php
require_once __DIR__.'/../helpers.php';

header('Content-Type: application/json');
echo json_encode(["user" => current_user()]);
