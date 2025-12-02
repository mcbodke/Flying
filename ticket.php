<?php
require 'inc/header.php';
require 'inc/auth.php';

$id = intval($_GET['id'] ?? 0);
if(!$id){ echo "<div class='alert alert-danger'>No ticket specified.</div>"; require 'inc/footer.php'; exit; }

$stmt = $mysqli->prepare("
  SELECT b.*, f.airline, f.flight_no, f.from_city, f.to_city, f.depart, f.arrive
  FROM book_flight b
  JOIN flights f ON f.id=b.flight_id
  WHERE b.id=? AND b.user_id=?
  LIMIT 1
");
$stmt->bind_param('ii', $id, $_SESSION['user_id']);
$stmt->execute(); $res = $stmt->get_result();
$ticket = $res->fetch_assoc();
$stmt->close();

if(!$ticket){ echo "<div class='alert alert-danger'>Ticket not found.</div>"; require 'inc/footer.php'; exit; }
?>
<div class="row">
  <div class="col-md-8 offset-md-2">
    <div class="card p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>e‑Ticket - Booking #<?=e($ticket['id'])?></h4>
        <div>
          <?php if($ticket['booking_status']==='confirmed'): ?>
            <a class="btn btn-success" href="/flying/download_ticket.php?id=<?=e($ticket['id'])?>">Download</a>
          <?php endif; ?>
          <button class="btn btn-outline-secondary" onclick="window.print()">Print</button>
        </div>
      </div>
      <hr>
      <div class="row">
        <div class="col-md-6">
          <p><strong>Passenger Name:</strong> <?=e($_SESSION['user_name'] ?? 'Customer')?></p>
          <p><strong>Passengers:</strong> <?=e($ticket['passengers'])?></p>
          <p><strong>Status:</strong>
            <?php if($ticket['booking_status']==='confirmed'): ?>
              <span class="badge bg-success">Confirmed</span>
            <?php else: ?>
              <span class="badge bg-warning text-dark"><?=e(ucfirst($ticket['booking_status']))?></span>
            <?php endif; ?>
          </p>
          <p><strong>Booked At:</strong> <?=date('d M Y H:i', strtotime($ticket['created_at']))?></p>
        </div>
        <div class="col-md-6">
          <p><strong>Flight:</strong> <?=e($ticket['airline'])?> (<?=e($ticket['flight_no'])?>)</p>
          <p><strong>Route:</strong> <?=e($ticket['from_city'])?> → <?=e($ticket['to_city'])?></p>
          <p><strong>Departure:</strong> <?=date('d M Y Y H:i', strtotime($ticket['depart']))?></p>
          <p><strong>Arrival:</strong> <?=date('d M Y H:i', strtotime($ticket['arrive']))?></p>
        </div>
      </div>
      <hr>
      <p><strong>Total Paid:</strong> ₹ <?=number_format($ticket['total_price'],2)?></p>
      <p class="text-muted">Please carry a valid ID and arrive at the airport at least 2 hours before departure.</p>
    </div>
  </div>
</div>
<?php require 'inc/footer.php'; ?>
