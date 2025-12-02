<?php
// api/search_flights.php
require_once __DIR__ . '/../inc/config.php';

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$date = $_GET['date'] ?? '';

$q = "SELECT id,airline,flight_no,from_city,to_city,depart,arrive,price,seats_available FROM flights WHERE 1=1";
$params = [];
$types = '';

if ($from !== '') {
  $q .= " AND from_city LIKE ?";
  $params[] = "%$from%";
  $types .= 's';
}
if ($to !== '') {
  $q .= " AND to_city LIKE ?";
  $params[] = "%$to%";
  $types .= 's';
}
if ($date !== '') {
  $q .= " AND DATE(depart)=?";
  $params[] = $date;
  $types .= 's';
}
$q .= " ORDER BY depart LIMIT 200";

$stmt = $mysqli->prepare($q);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
$rows = [];
while ($r = $res->fetch_assoc()) {
  $r['depart'] = $r['depart'];
  $r['arrive'] = $r['arrive'];
  $rows[] = $r;
}
json_response(['status' => 'ok', 'data' => $rows]);
