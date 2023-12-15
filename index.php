<?php
// začetek seje
session_start();
$_SESSION['ime'] = "";
// če vinogradnik ni še prijavlejn
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Slovenske Trte</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
  .fakeimg {
    height: 200px;
    background: #aaa;
  }
  </style>
  <link href="style.css" rel="stylesheet">
</head>
<body>

<div class="p-5 bg-primary text-white text-center">
  <h1>Slovenske Trte</h1>
</div>

<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  <div class="container-fluid">
    <ul class="navbar-nav" id = "nav-items">
      <li class="nav-item">
        <a class="nav-link active" href="index.php">
        <img src="Slike/logo.png" alt="Avatar Logo" style="width:37px;" class="rounded-pill">
        </a>
      </li>
      <?php if(isset($_SESSION['vinogradnik_ID'])): ?>
      <li class="nav-item">
        <a class="nav-link" href="odjava.php">Odjava</a>
      </li>
      <?php else: ?>
      <li class="nav-item">
        <a class="nav-link" href="prijava.php">Prijava</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="registracija.php">Registracija</a>
      </li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

<!-- Izbira pridelkov, zemljišč ali vinogradnikov-->
<section id="services" class="services section-bg">
      <div class="container mb-4" data-aos="fade-up">
        <div class="row gy-4">
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="service-item  position-relative">
              <div class="icon">
                <i class="fa-solid fa-mountain-city"></i>
              </div>
              <h3>Pridelek</h3>
              <p>Seznam vseh pridelkov vinogradnikov.</p>
              <a href="pridelki_menu.php" class="readmore stretched-link">Learn more <i
                  class="bi bi-arrow-right"></i></a>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fa-solid fa-arrow-up-from-ground-water"></i>
              </div>
              <h3>Zemljišče</h3>
              <p>Seznam vseh zemljišč vinogradnikov.</p>
              <a href="zemljisca_menu.php" class="readmore stretched-link">Learn more <i
                  class="bi bi-arrow-right"></i></a>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="service-item position-relative">
              <div class="icon">
                <i class="fa-solid fa-compass-drafting"></i>
              </div>
              <h3>Vinogradniki</h3>
              <p>Seznam vseh vinogradnikov.</p>
              <a href="vinogradniki_menu.php" class="readmore stretched-link">Learn more <i
                  class="bi bi-arrow-right"></i></a>
            </div>
          </div><!-- End Service Item -->

          <!-- End Service Item -->

        </div>

      </div>
    </section><!-- End Services Section -->
<div class="p-3 bg-dark text-white text-center fixed-bottom">
  <a href="viri.php">Viri</a>
</div>
</body>
</html>