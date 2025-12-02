<?php
require 'inc/header.php';
require 'inc/auth.php';

if (empty($_SESSION['user_id'])) {
  header('Location: /flying/login.php');
  exit;
}

$flight_id = intval($_GET['flight_id'] ?? 0);
if (!$flight_id) {
  echo "<div class='alert alert-danger'>No flight selected.</div>";
  require 'inc/footer.php';
  exit;
}

$stmt = $mysqli->prepare("SELECT * FROM flights WHERE id=? LIMIT 1");
$stmt->bind_param('i', $flight_id);
$stmt->execute();
$res = $stmt->get_result();
$flight = $res->fetch_assoc();
$stmt->close();
if (!$flight) {
  echo "<div class='alert alert-danger'>Flight not found.</div>";
  require 'inc/footer.php';
  exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $passengers = max(1, intval($_POST['passengers'] ?? 1));
  if ($passengers > $flight['seats_available']) $errors[] = "Not enough seats available.";
  if (empty($errors)) {
    $total = $flight['price'] * $passengers;
    $stmt = $mysqli->prepare("INSERT INTO book_flight (user_id,flight_id,passengers,total_price) VALUES (?,?,?,?)");
    $stmt->bind_param('iiid', $_SESSION['user_id'], $flight_id, $passengers, $total);
    if ($stmt->execute()) {
      $booking_id = $stmt->insert_id;
      // reduce seats
      $stmt2 = $mysqli->prepare("UPDATE flights SET seats_available=seats_available-? WHERE id=?");
      $stmt2->bind_param('ii', $passengers, $flight_id);
      $stmt2->execute();
      $stmt2->close();
      header("Location: /flying/payment.php?booking_id=$booking_id");
      exit;
    } else {
      $errors[] = "DB error: " . $stmt->error;
    }
    $stmt->close();
  }
}
?>
<div class="row">
  <div class="col-md-8 offset-md-2">
    <div class="card p-3">
      <h4>Booking - <?= e($flight['airline']) ?> (<?= e($flight['flight_no']) ?>)</h4>
      <p><?= e($flight['from_city']) ?> → <?= e($flight['to_city']) ?> | <?= date('d M Y H:i', strtotime($flight['depart'])) ?></p>
      <p>Price per passenger: ₹ <?= number_format($flight['price'], 2) ?></p>
      <?php foreach ($errors as $er) echo "<div class='alert alert-danger'>" . e($er) . "</div>"; ?>
      <form method="post" id="bookForm">
        <div class="mb-3"><label>Passengers<input type="number" name="passengers" class="form-control" value="1" min="1" max="<?= e($flight['seats_available']) ?>"></label></div>
        <button class="btn btn-success">Proceed to Payment</button>
      </form>
    </div>
  </div>
</div>
<?php require 'inc/footer.php'; ?>