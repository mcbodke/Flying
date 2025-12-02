<?php
require 'inc/header.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $stmt = $mysqli->prepare("SELECT id,password_hash,full_name FROM users WHERE username=? LIMIT 1");
  $stmt->bind_param('s', $username);
  $stmt->execute();
  $stmt->bind_result($id, $hash, $name);
  if ($stmt->fetch() && password_verify($password, $hash)) {
    $_SESSION['user_id'] = $id;
    $_SESSION['user_name'] = $name;
    header('Location: /flying/index.php');
    exit;
  } else {
    $err = "Invalid username or password.";
  }
  $stmt->close();
}
if (!empty($_GET['registered'])) echo "<div class='alert alert-success'>Registration successful — please login.</div>";
?>
<div class="row">
  <div class="col-md-4 offset-md-4">
    <div class="card p-4">
      <h4>Login</h4>
      <?php if ($err) echo "<div class='alert alert-danger'>" . e($err) . "</div>"; ?>
      <form method="post">
        <div class="mb-3"><input class="form-control" name="username" placeholder="Username" required></div>
        <div class="mb-3"><input class="form-control" name="password" type="password" placeholder="Password" required></div>
        <button class="btn btn-primary w-100">Login</button>
      </form>
      <div class="mt-3 text-center"><a href="/flying/register.php">Create account</a></div>
    </div>
  </div>
</div>
<?php require 'inc/footer.php'; ?>