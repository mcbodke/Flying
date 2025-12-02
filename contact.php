<?php require 'inc/header.php'; ?>
<div class="card p-3">
  <h4>Contact Us</h4>
  <form method="post" action="/flying/contact.php">
    <div class="mb-3"><input class="form-control" name="name" placeholder="Your name"></div>
    <div class="mb-3"><input class="form-control" name="email" placeholder="Your email"></div>
    <div class="mb-3"><textarea class="form-control" name="message" placeholder="Message"></textarea></div>
    <button class="btn btn-primary">Send Message</button>
  </form>
</div>
<?php require 'inc/footer.php'; ?>
