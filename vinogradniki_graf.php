<?php
// Start the session
session_start();
// povezava na bazo
$conn = mysqli_connect('localhost', 'id21675710_trte', 'Trte123&', 'id21675710_slovensketrte');

// podatki za graf o število zemljišč, ki si jih vsak vinogradnik lasti
$stmt = mysqli_prepare($conn, "SELECT v.ID as oznaka, v.ime as oznaka2, v.priimek as oznaka3, COUNT(z.ID) as kolicina FROM vinogradnik v INNER JOIN zemljisce z ON v.ID = z.vinogradnik_ID GROUP BY v.ID");
mysqli_stmt_execute($stmt);
$rezultat1 = mysqli_stmt_get_result($stmt);
$pod1 = array();
while ($row = mysqli_fetch_assoc($rezultat1)) {
  $pod1[] = $row;
}

// podatki za graf o zaslužku na vinogranika
$stmt = mysqli_prepare($conn, "SELECT v.ID as oznaka, SUM(kolicina_prodanega_pridelka * cena) as kolicina FROM pridelek p INNER JOIN zemljisce z ON z.ID = p.zemljisce_ID INNER JOIN vinogradnik v ON v.ID = z.vinogradnik_ID GROUP BY v.ID");
mysqli_stmt_execute($stmt);
$rezultat2 = mysqli_stmt_get_result($stmt);
$pod2 = array();
while ($row = mysqli_fetch_assoc($rezultat2)) {
  $pod2[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Slovenske Trte</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="style.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid p-5 bg-primary text-white text-center">
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
<div class="container-fluid" data-aos="fade-up">
  <div class="chart-container container p-2 mt-5 bg-light" style="height: 50.1vh;">
    <canvas id="graf"></canvas>
  </div>
  <div class="container mt-4 text-center">
      <button id="prikaz1" class="btn btn-primary mt-1">Prikaži število zemljišč na vinogradnika</button>
      <button id="prikaz2" class="btn btn-primary mt-1">Prikaži zaslužek vinogradnikov</button>
  </div>
  <div class="container mt-3 mb-3 text-center">
      <button id="nazaj" class="btn btn-primary">Nazaj</button>
  </div>
</div>
<div class="p-3 bg-dark text-white text-center fixed-bottom">
  <a href="viri.php">Viri</a>
</div>
<script>
    var pod1 = <?php echo json_encode($pod1); ?>;
    var pod2 = <?php echo json_encode($pod2); ?>;
    var chart = null;

    // Function to create a pie chart with the given data
    function createPieChart(data) {
    var ctx = document.getElementById('graf').getContext('2d');
    chart = new Chart(ctx, {
        type: 'pie',
        data: {
        labels: data.map(d => d.oznaka + " " + d.oznaka2 + " " +d.oznaka3),
        datasets: [{
            label: oznaka1,
            data: data.map(d => d.kolicina),
            backgroundColor: ['#FF0027', '#A7FF00', '#00FFD8', '#5800FF', '#FFBA00']
        }]
        },
        options: {
        responsive: true,
        maintainAspectRatio: false
        }
    });
    }
    var oznaka1 = 'Število zemljišč v lasti';
    createPieChart(pod1);
    // Show producers data when "Show Producers" button is clicked
    document.getElementById('prikaz1').addEventListener('click', function() {
    if (chart) {
        chart.destroy();
    }
    oznaka1 = 'Število zemljišč v lasti';
    createPieChart(pod1);
    });

    // Show varieties data when "Show Varieties" button is clicked
    document.getElementById('prikaz2').addEventListener('click', function() {
    if (chart) {
        chart.destroy();
    }
    oznaka1 = 'Število trt na velikost zemljišča';
    createPieChart(pod2);
    });

    document.getElementById("nazaj").onclick = function () {
      location.href = "vinogradniki.php";
    };
</script>
</body>
</html>
