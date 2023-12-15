<?php
// Start the session
session_start();
// povezava na bazo
$conn = mysqli_connect('localhost', 'root', '', 'slovensketrte');

$ID = $_POST['ID'];
$vinogradnik_ID = $_POST['vinogradnik_ID'];

//poiščemo še ostale podatke o zemljišču
$stmt = mysqli_prepare($conn, "SELECT z.ID, velikost, kolicina_trt, vinogradnik_ID, ime, priimek, naslov, telefon, e_posta FROM zemljisce z INNER JOIN vinogradnik v ON z.vinogradnik_ID = v.ID WHERE v.ID = ? AND z.ID = ?");
mysqli_stmt_bind_param($stmt, "ii", $vinogradnik_ID, $ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// asociativna tabela
$data = array();
while ($row = mysqli_fetch_assoc($result)) {
  // npr $data['ID'] = 5;
  $data[] = $row;
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
  
<div class="container mt-3">
    <?php foreach ($data as $row){ ?>
      <ul class = "list-group mb-2">
        <li class="list-group-item list-group-item-info"><b>Lastnosti zemljišča</b></li>
        <li class="list-group-item">ID: <?php echo $row['ID']; ?></li>
        <li class="list-group-item">velikost zemljišča: <?php echo $row['velikost']; ?></li>
        <li class="list-group-item">število trt na zemljišču: <?php echo $row['kolicina_trt']; ?></li>
      </ul>
      <ul class = "list-group mb-2">
        <li class="list-group-item list-group-item-info"><b>Lastnosti vinogradnika</b></li>
        <li class="list-group-item">ID: <?php echo $row['vinogradnik_ID']; ?></li>
        <li class="list-group-item">ime: <?php echo $row['ime']; ?></li>
        <li class="list-group-item">priimek: <?php echo $row['priimek']; ?></li>
        <li class="list-group-item">naslov: <?php echo $row['naslov']; ?></li>
        <li class="list-group-item">telefon: <?php echo $row['telefon']; ?></li>
        <li class="list-group-item">e-poštni naslov: <?php echo $row['e_posta']; ?>
      </ul>
    <?php }?>
</div>

<div class="container mt-3 mb-5 text-center">
  <button id="nazaj" class="btn btn-primary">Nazaj</button>
</div>
<div class="p-3 bg-dark text-white text-center fixed-bottom">
  <a href="viri.php">Viri</a>
</div>
<script type="text/javascript">
  document.getElementById("nazaj").onclick = function () {
    location.href = "index.php";
  };
</script>
</body>
</html>
