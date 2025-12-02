<?php
require 'inc/header.php';
require 'inc/auth.php';

$booking_id = intval($_GET['booking_id'] ?? 0);
if(!$booking_id) { echo "<div class='alert alert-danger'>No booking specified.</div>"; require 'inc/footer.php'; exit; }

$stmt = $mysqli->prepare("SELECT b.*, f.airline, f.flight_no FROM book_flight b JOIN flights f ON f.id=b.flight_id WHERE b.id=? AND b.user_id=? LIMIT 1");
$stmt->bind_param('ii', $booking_id, $_SESSION['user_id']); $stmt->execute(); $res = $stmt->get_result();
$booking = $res->fetch_assoc(); $stmt->close();
if(!$booking){ echo "<div class='alert alert-danger'>Booking not found.</div>"; require 'inc/footer.php'; exit; }

$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
  // In real app: call gateway. Here: mark payment success
  $method = $_POST['method'] ?? 'card';
  $amount = $booking['total_price'];
  $stmt = $mysqli->prepare("INSERT INTO payment (booking_id,user_id,amount,payment_method,payment_status,transaction_id,paid_at) VALUES (?,?,?,?,? ,?,NOW())");
  $txn = 'TXN'.time().rand(100,999);
  $status = 'success';
  $stmt->bind_param('iiisss', $booking_id, $_SESSION['user_id'], $amount, $method, $status, $txn);
  if($stmt->execute()){
    $pid = $stmt->insert_id;
    $stmt2 = $mysqli->prepare("UPDATE book_flight SET booking_status='confirmed' WHERE id=?");
    $stmt2->bind_param('i', $booking_id); $stmt2->execute(); $stmt2->close();
    header('Location: /flying/index.php?paid=1'); exit;
  } else {
    $errors[] = "Payment failed: ".$stmt->error;
  }
  $stmt->close();
}

?>
<div class="row">
  <div class="col-md-6 offset-md-3">
    <div class="card p-3">
      <h4>Payment</h4>
      <p>Flight: <?=e($booking['airline'])?> (<?=e($booking['flight_no'])?>)</p>
      <p>Passengers: <?=e($booking['passengers'])?></p>
      <p><strong>Amount: ₹ <?=number_format($booking['total_price'],2)?></strong></p>
      <?php foreach($errors as $er) echo "<div class='alert alert-danger'>".e($er)."</div>"; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Payment method
            <select name="method" class="form-select">
              <option value="card">Card</option>
              <option value="upi">UPI</option>
              <option value="counter">Pay at counter</option>
            </select>
          </label>
        </div>
        <!-- Demo: don't collect card details in production -->
        <button class="btn btn-success w-100">Pay Now</button>
      </form>
    </div>
  </div>
</div>
<?php require 'inc/footer.php'; ?>
