 <?php
require 'inc/config.php';
require 'inc/auth.php';

$id = intval($_GET['id'] ?? 0);
if(!$id){ http_response_code(400); echo "Invalid request."; exit; }

$stmt = $mysqli->prepare("
  SELECT b.*, f.airline, f.flight_no, f.from_city, f.to_city, f.depart, f.arrive
  FROM book_flight b
  JOIN flights f ON f.id=b.flight_id
  WHERE b.id=? AND b.user_id=? AND b.booking_status='confirmed'
  LIMIT 1
");
$stmt->bind_param('ii', $id, $_SESSION['user_id']);
$stmt->execute(); $res = $stmt->get_result();
$t = $res->fetch_assoc();
$stmt->close();

if(!$t){ http_response_code(404); echo "Ticket not found or not confirmed."; exit; }

$filename = "ticket-".$t['id']."-".preg_replace('/[^A-Za-z0-9\-]/','', $t['flight_no']).".html";
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: attachment; filename="'.$filename.'"');
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Ticket #<?=e($t['id'])?></title>
  <style>
    body{ font-family: Arial, sans-serif; margin:20px; }
    .ticket{ border:1px solid #ccc; padding:16px; }
    .row{ display:flex; gap:24px; }
    .col{ flex:1; }
    .badge{ display:inline-block; padding:3px 8px; border-radius:4px; background:#198754; color:#fff; font-size:12px; }
    .muted{ color:#666; font-size:12px; }
    h2,h3,h4{ margin:0 0 8px 0; }
    table{ width:100%; border-collapse:collapse; margin-top:10px; }
    td{ padding:6px 0; vertical-align:top; }
  </style>
</head>
<body>
  <div class="ticket">
    <h2>e‑Ticket</h2>
    <div class="muted">Booking #<?=e($t['id'])?> | Generated: <?=date('d M Y H:i')?></div>
    <hr>
    <div class="row">
      <div class="col">
        <h4>Passenger</h4>
        <table>
          <tr><td><strong>Name:</strong></td><td><?=e($_SESSION['user_name'] ?? 'Customer')?></td></tr>
          <tr><td><strong>Passengers:</strong></td><td><?=e($t['passengers'])?></td></tr>
          <tr><td><strong>Status:</strong></td><td><span class="badge">CONFIRMED</span></td></tr>
          <tr><td><strong>Booked At:</strong></td><td><?=date('d M Y H:i', strtotime($t['created_at']))?></td></tr>
        </table>
      </div>
      <div class="col">
        <h4>Flight</h4>
        <table>
          <tr><td><strong>Airline:</strong></td><td><?=e($t['airline'])?></td></tr>
          <tr><td><strong>Flight No:</strong></td><td><?=e($t['flight_no'])?></td></tr>
          <tr><td><strong>Route:</strong></td><td><?=e($t['from_city'])?> → <?=e($t['to_city'])?></td></tr>
          <tr><td><strong>Departure:</strong></td><td><?=date('d M Y H:i', strtotime($t['depart']))?></td></tr>
          <tr><td><strong>Arrival:</strong></td><td><?=date('d M Y H:i', strtotime($t['arrive']))?></td></tr>
          <tr><td><strong>Total Paid:</strong></td><td>₹ <?=number_format($t['total_price'],2)?></td></tr>
        </table>
      </div>
    </div>
    <hr>
    <div class="muted">This is a system-generated ticket. Please carry a valid ID.</div>
  </div>
</body>
</html>
