<?php
// Start the session
session_start();
// ponastavi velikost v primeru, če uporabnik gre s puščico nazaj
$_SESSION['velikost'] = "";
if (!isset($_SESSION['vinogradnik_ID'])) {
  header('Location: prijava.php');
  exit;
}

// povezava na bazo
$conn = mysqli_connect('localhost', 'id21675710_trte', 'Trte123&', 'id21675710_slovensketrte');
$vinogradnik_ID = $_SESSION['vinogradnik_ID'];
$stmt = mysqli_prepare($conn, "SELECT ID, velikost, kolicina_trt FROM zemljisce WHERE vinogradnik_ID = ?");
mysqli_stmt_bind_param($stmt, "i", $vinogradnik_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (isset($_POST['izbrisi'])) {
  // izbrišemo tudi vsa pridelek tega zemljišča
  $stmt = mysqli_prepare($conn, "DELETE FROM pridelek WHERE zemljisce_ID = ?");
  mysqli_stmt_bind_param($stmt, "i", $_POST['ID_zemljisce']);
  mysqli_stmt_execute($stmt);

  $stmt = mysqli_prepare($conn, "DELETE FROM zemljisce WHERE ID = ?");
  mysqli_stmt_bind_param($stmt, "i", $_POST['ID_zemljisce']);
  mysqli_stmt_execute($stmt);
  header('Location: zemljisca.php');
  exit;
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
  
<div class="container mt-5">
  <table class="table">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Velikost</th>
        <th>Količina trt</th>
        <th>Urejanje</th>
        <th>Brisanje</th>
      </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['ID']; ?></td>
                <td><?php echo $row['velikost']; ?></td>
                <td><?php echo $row['kolicina_trt']; ?></td>
                <td>
                    <form method="POST" action="zemljisca_uredi.php">
                        <input type="hidden" name="ID" value="<?php echo $row['ID']; ?>">
                        <input type="submit" class="btn btn-primary" name="uredi" value="Uredi">
                    </form>
                </td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="ID_zemljisce" value="<?php echo $row['ID']; ?>">
                        <input type="submit" class="btn btn-primary" name="izbrisi" value="Izbrisi">
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
  </table>
</div>
<div class="container mt-3 mb-3 text-center">
  <button id="nazaj" class="btn btn-primary">Nazaj</button>
</div>
<div class="p-3 bg-dark text-white text-center fixed-bottom">
  <a href="viri.php">Viri</a>
</div>
<script type="text/javascript">
  document.getElementById("nazaj").onclick = function () {
    location.href = "zemljisca_menu.php";
  };
</script>
</body>
</html>
