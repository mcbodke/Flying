<?php
require 'inc/header.php';

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$date = $_GET['date'] ?? '';

$q = "SELECT * FROM flights WHERE 1=1";
$params = [];
$types = '';

if($from !== ''){
  $q .= " AND from_city LIKE ?";
  $params[] = "%$from%"; $types .= 's';
}
if($to !== ''){
  $q .= " AND to_city LIKE ?";
  $params[] = "%$to%"; $types .= 's';
}
if($date !== ''){
  $q .= " AND DATE(depart)=?";
  $params[] = $date; $types .= 's';
}
$q .= " ORDER BY depart ASC LIMIT 100";

$stmt = $mysqli->prepare($q);
if($params){
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();
?>
<h3>Search Results</h3>
<?php if($res->num_rows===0): ?>
  <div class="alert alert-warning">No flights found.</div>
<?php else: ?>
  <div class="row gy-3">
    <?php while($f = $res->fetch_assoc()): ?>
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h5><?=e($f['airline'])?> — <?=e($f['flight_no'])?></h5>
            <p><?=e($f['from_city'])?> → <?=e($f['to_city'])?></p>
            <p><?=date('d M Y H:i', strtotime($f['depart']))?> — <?=date('H:i', strtotime($f['arrive']))?></p>
            <p><strong>₹ <?=number_format($f['price'],2)?></strong> — Seats: <?=e($f['seats_available'])?></p>
            <a href="/flying/book_flight.php?flight_id=<?=e($f['id'])?>" class="btn btn-primary">Book</a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
<?php endif; ?>
<?php require 'inc/footer.php'; ?>
