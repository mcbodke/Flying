<?php
require 'inc/header.php';
?>

<!-- Hero banner -->
<section class="hero-section">
  <div class="container hero-inner">
    <div class="hero-text">
      <h1 class="hero-title">Time to <span>Travel</span></h1>
      <p class="hero-subtitle">
        Book fast, fly smart. Discover the best routes and fares in a few clicks.
      </p>
    </div>
  </div>
</section>

<!-- Central search panel -->
<section class="search-panel-section">
  <div class="container">
    <div class="search-panel card">
      <div class="card-body">
        <h3 class="mb-3">Search &amp; Book Flights</h3>
        <form id="searchForm" method="get" action="/flying/flight_search.php" class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label">From</label>
            <input name="from" class="form-control" placeholder="From city">
          </div>
          <div class="col-md-3">
            <label class="form-label">To</label>
            <input name="to" class="form-control" placeholder="To city">
          </div>
          <div class="col-md-3">
            <label class="form-label">Departure date</label>
            <input name="date" type="date" class="form-control">
          </div>
          <div class="col-md-3 d-grid">
            <button class="btn btn-primary btn-lg mt-md-4">Find my flight</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Popular flights table style -->
<section class="popular-flights-section py-5">
  <div class="container">
    <h4 class="section-title text-center mb-4">Available Flights</h4>
    <div class="card flights-table-card">
      <div class="card-body table-responsive">
        <table class="table table-hover align-middle mb-0 flights-table">
          <thead>
            <tr>
              <th>Airline</th>
              <th>Flight</th>
              <th>From</th>
              <th>To</th>
              <th>Departure</th>
              <th>Price</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          <?php
          $stmt = $mysqli->prepare("SELECT id,airline,flight_no,from_city,to_city,depart,price FROM flights ORDER BY depart LIMIT 6");
          $stmt->execute(); $res = $stmt->get_result();
          while($f = $res->fetch_assoc()):
          ?>
            <tr>
              <td><?=e($f['airline'])?></td>
              <td><?=e($f['flight_no'])?></td>
              <td><?=e($f['from_city'])?></td>
              <td><?=e($f['to_city'])?></td>
              <td><?=date('d M Y H:i', strtotime($f['depart']))?></td>
              <td><strong>₹ <?=number_format($f['price'],2)?></strong></td>
              <td>
                <a href="/flying/book_flight.php?flight_id=<?=e($f['id'])?>" class="btn btn-sm btn-outline-primary">
                  Book
                </a>
              </td>
            </tr>
          <?php endwhile; $stmt->close(); ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<!-- Our Advantages -->
<section class="advantages-section py-5">
  <div class="container text-center">
    <h4 class="section-title mb-4">Our Advantages</h4>
    <div class="row g-4">
      <div class="col-md-3 col-6">
        <div class="advantage-card">
          <div class="icon-circle">★</div>
          <h6>Best Fares</h6>
          <p>Competitive pricing across major airlines and routes.</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="advantage-card">
          <div class="icon-circle">☺</div>
          <h6>Customer Experience</h6>
          <p>Simple booking flow and instant ticket downloads.</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="advantage-card">
          <div class="icon-circle">🔍</div>
          <h6>Powerful Search</h6>
          <p>Filter flights quickly by date, route, and price.</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="advantage-card">
          <div class="icon-circle">💳</div>
          <h6>Secure Payments</h6>
          <p>Safe checkout for every booking you make.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require 'inc/footer.php'; ?>
