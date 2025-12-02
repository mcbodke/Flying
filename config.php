<?php
// inc/config.php
declare(strict_types=1);
session_start();

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = ''; // change
$DB_NAME = 'my_flying';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    http_response_code(500);
    die("DB connect failed: " . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// simple JSON helper
function json_response($data, $code = 200){
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data);
  exit;
}
?>
