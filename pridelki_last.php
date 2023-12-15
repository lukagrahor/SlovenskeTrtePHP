<?php
// Start the session
session_start();
$_SESSION['vrsta'] = "";
if (!isset($_SESSION['vinogradnik_ID'])) {
  header('Location: prijava.php');
  exit;
}
// povezava na bazo
$conn = mysqli_connect('localhost', 'root', '', 'slovensketrte');
$vinogradnik_ID = $_SESSION['vinogradnik_ID'];
$stmt = mysqli_prepare($conn, "SELECT p.ID, vrsta, kolicina_pridelka, kolicina_prodanega_pridelka, cena, zemljisce_ID FROM pridelek p INNER JOIN zemljisce z on p.zemljisce_ID = z.ID WHERE vinogradnik_ID = ?;");
mysqli_stmt_bind_param($stmt, "i", $vinogradnik_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);  
if (isset($_POST['izbrisi'])) {
  $stmt = mysqli_prepare($conn, "DELETE FROM pridelek WHERE ID = ?");
  mysqli_stmt_bind_param($stmt, "i", $_POST['ID_pridelek']);
  mysqli_stmt_execute($stmt);
  header('Location: pridelki.php');
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
        <th>Vrsta</th>
        <th>Količina pridelka</th>
        <th>Količina prodanega pridelka</th>
        <th>Cena na kg</th>
        <th>ID zemljišča</th>
        <th>Urejanje</th>
        <th>Brisanje</th>
      </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['ID']; ?></td>
                <td><?php echo $row['vrsta']; ?></td>
                <td><?php echo $row['kolicina_pridelka']; ?></td>
                <td><?php echo $row['kolicina_prodanega_pridelka']; ?></td>
                <td><?php echo $row['cena']; ?></td>
                <td><?php echo $row['zemljisce_ID']; ?></td>
                <td>
                    <form method="POST" action="pridelki_uredi.php">
                        <input type="hidden" name="ID" value="<?php echo $row['ID']; ?>">
                        <input type="hidden" name="zemljisce_ID" value="<?php echo $row['zemljisce_ID']; ?>">
                        <input type="submit" class="btn btn-primary" name="uredi" value="Uredi">
                    </form>
                </td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="ID_pridelek" value="<?php echo $row['ID']; ?>">
                        <input type="submit" class="btn btn-primary" name="izbrisi" value="Izbriši">
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
</div>
<div class="p-3 bg-dark text-white text-center fixed-bottom">
  <a href="viri.php">Viri</a>
</div>
<script type="text/javascript">
  document.getElementById("nazaj").onclick = function () {
    location.href = "pridelki_menu.php";
  };
</script>

</body>
</html>
