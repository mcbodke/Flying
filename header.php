<?php
require_once __DIR__.'/config.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Flying</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/flying/assets/css/style.css">
</head>
<body>

<!-- Top info bar -->
<header class="top-header py-1">
  <div class="container d-flex justify-content-between align-items-center small text-light">
    <div class="d-flex align-items-center gap-2">
      <span class="fw-semibold">My Flying</span>
      <span class="d-none d-md-inline">| Book your best flight experience</span>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="d-none d-md-inline">24/7 Support: <strong>1800-000-999</strong></span>
      <a href="/flying/contact.php" class="link-light text-decoration-none d-none d-md-inline">Help &amp; Support</a>
    </div>
  </div>
</header>

<!-- Main navigation -->
<nav class="navbar navbar-expand-lg main-navbar">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="/flying/index.php">
      <span class="brand-logo rounded-circle d-inline-flex align-items-center justify-content-center">
        ✈
      </span>
      <span class="brand-text">
        <span class="d-block fw-bold">My Flying</span>
        <small class="text-muted d-none d-md-block">Time to travel</small>
      </span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div id="navMain" class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto me-3">
        <li class="nav-item"><a class="nav-link" href="/flying/index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="/flying/flight_search.php">Flights</a></li>
        <li class="nav-item"><a class="nav-link" href="/flying/about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="/flying/contact.php">Contact</a></li>
      </ul>
      <ul class="navbar-nav auth-nav">
        <?php if(!empty($_SESSION['user_id'])): ?>
          <li class="nav-item d-none d-lg-block"><a class="nav-link" href="/flying/my_tickets.php">My Tickets</a></li>
          <li class="nav-item d-none d-lg-block"><span class="nav-link">Hi, <?= e($_SESSION['user_name'] ?? 'User')?></span></li>
          <li class="nav-item"><a class="btn btn-sm btn-outline-light" href="/flying/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item me-2"><a class="btn btn-sm btn-outline-light" href="/flying/login.php">Login</a></li>
          <li class="nav-item"><a class="btn btn-sm btn-accent" href="/flying/register.php">Sign up</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<main class="page-main">
