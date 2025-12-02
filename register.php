<?php
require 'inc/header.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';
  $email = trim($_POST['email'] ?? '');
  $full_name = trim($_POST['full_name'] ?? '');
  if (!$username || !$password || !$email) {
    $errors[] = "All fields required.";
  }
  if (empty($errors)) {
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE username=? OR email=? LIMIT 1");
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) $errors[] = "Username or email already exists.";
    $stmt->close();
  }
  if (empty($errors)) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("INSERT INTO users (username,password_hash,full_name,email) VALUES (?,?,?,?)");
    $stmt->bind_param('ssss', $username, $hash, $full_name, $email);
    if ($stmt->execute()) {
      header('Location: login.php?registered=1');
      exit;
    } else {
      $errors[] = "DB error: " . $stmt->error;
    }
    $stmt->close();
  }
}
require 'inc/header.php';
?>
<div class="row">
  <div class="col-md-6 offset-md-3">
    <div class="card p-4">
      <h4>Register</h4>
      <?php foreach ($errors as $err) echo "<div class='alert alert-danger'>" . e($err) . "</div>"; ?>
      <form method="post">
        <div class="mb-3"><label class="form-label">Full name<input name="full_name" class="form-control"></label></div>
        <div class="mb-3"><label class="form-label">Username<input name="username" class="form-control" required></label></div>
        <div class="mb-3"><label class="form-label">Email<input type="email" name="email" class="form-control" required></label></div>
        <div class="mb-3"><label class="form-label">Password<input type="password" name="password" class="form-control" required></label></div>
        <button class="btn btn-primary">Register</button>
      </form>
    </div>
  </div>
</div>
<?php require 'inc/footer.php'; ?>