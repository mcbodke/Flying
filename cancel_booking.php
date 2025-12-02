<?php
require 'inc/config.php';
require 'inc/auth.php';

if(empty($_SESSION['user_id'])) { header('Location: /flying/login.php'); exit; }

$id = intval($_GET['id'] ?? 0);
if(!$id){ http_response_code(400); echo "Invalid request."; exit; }

// Fetch booking (ensure owner and pending)
$stmt = $mysqli->prepare("SELECT id, flight_id, passengers, booking_status FROM book_flight WHERE id=? AND user_id=? LIMIT 1");
$stmt->bind_param('ii', $id, $_SESSION['user_id']);
$stmt->execute(); $res = $stmt->get_result();
$bk = $res->fetch_assoc();
$stmt->close();

if(!$bk){ http_response_code(404); echo "Booking not found."; exit; }
if($bk['booking_status'] !== 'pending'){
  header('Location: /flying/my_tickets.php'); exit;
}

// Begin cancel: delete booking and restore seats
$mysqli->begin_transaction();
try {
  // restore seats first
  $stmt2 = $mysqli->prepare("UPDATE flights SET seats_available = seats_available + ? WHERE id=?");
  $stmt2->bind_param('ii', $bk['passengers'], $bk['flight_id']);
  $stmt2->execute();
  $stmt2->close();

  // delete the booking row
  $stmt1 = $mysqli->prepare("DELETE FROM book_flight WHERE id=? AND user_id=? AND booking_status='pending'");
  $stmt1->bind_param('ii', $id, $_SESSION['user_id']);
  $stmt1->execute();
  $stmt1->close();

  $mysqli->commit();
  header('Location: /flying/my_tickets.php'); exit;
} catch (Throwable $e) {
  $mysqli->rollback();
  http_response_code(500);
  echo "Failed to cancel booking.";
  exit;
}