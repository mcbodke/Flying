<?php
require 'inc/header.php';
require 'inc/auth.php';

if(empty($_SESSION['user_id'])) { header('Location: /flying/login.php'); exit; }

// Fetch user's bookings with flight info and latest payment status (if any)
$stmt = $mysqli->prepare("
  SELECT b.id AS booking_id, b.created_at, b.passengers, b.total_price, b.booking_status,
         f.airline, f.flight_no, f.from_city, f.to_city, f.depart, f.arrive
  FROM book_flight b
  JOIN flights f ON f.id = b.flight_id
  WHERE b.user_id = ?
  ORDER BY b.created_at DESC
");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$res = $stmt->get_result();
?>
<h3>My Tickets</h3>

<?php if($res->num_rows === 0): ?>
  <div class="alert alert-info">You have no bookings yet.</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>Booking #</th>
          <th>Flight</th>
          <th>Route</th>
          <th>Departure</th>
          <th>Passengers</th>
          <th>Total (₹)</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php while($b = $res->fetch_assoc()): ?>
        <tr>
          <td><?=e($b['booking_id'])?></td>
          <td><?=e($b['airline'])?> (<?=e($b['flight_no'])?>)</td>
          <td><?=e($b['from_city'])?> → <?=e($b['to_city'])?></td>
          <td><?=date('d M Y H:i', strtotime($b['depart']))?></td>
          <td><?=e($b['passengers'])?></td>
          <td><?=number_format($b['total_price'], 2)?></td>
          <td>
            <?php if($b['booking_status']==='confirmed'): ?>
              <span class="badge bg-success">Confirmed</span>
            <?php elseif($b['booking_status']==='pending'): ?>
              <span class="badge bg-warning text-dark">Pending</span>
            <?php else: ?>
              <span class="badge bg-secondary"><?=e($b['booking_status'])?></span>
            <?php endif; ?>
          </td>
          <td>
            <a class="btn btn-sm btn-outline-primary" href="/flying/ticket.php?id=<?=e($b['booking_id'])?>">View</a>
            <?php if($b['booking_status']==='confirmed'): ?>
              <a class="btn btn-sm btn-success" href="/flying/download_ticket.php?id=<?=e($b['booking_id'])?>">Download</a>
            <?php elseif($b['booking_status']==='pending'): ?>
              <a class="btn btn-sm btn-warning" href="/flying/payment.php?booking_id=<?=e($b['booking_id'])?>">Pay Now</a>
              <a class="btn btn-sm btn-outline-danger" href="/flying/cancel_booking.php?id=<?=e($b['booking_id'])?>"
                 onclick="return confirm('Cancel this booking? Seats will be released.');">Cancel</a>
            <?php else: ?>
              <button class="btn btn-sm btn-secondary" disabled>Download</button>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; $stmt->close(); ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require 'inc/footer.php'; ?>