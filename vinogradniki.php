<?php
session_start();
// povezava na bazo
$conn = mysqli_connect('localhost', 'id21675710_trte', 'Trte123&', 'id21675710_slovensketrte');

$stmt = mysqli_prepare($conn, "SELECT ID, ime, priimek, naslov, telefon, e_posta FROM vinogradnik");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

//nastavimo privzete vrednosti
$atribut = isset($_GET['atribut']) ? $_GET['atribut'] : 'ID';
$smer = isset($_GET['smer']) ? $_GET['smer'] : 'ASC';

// asociativna tabela
$data = array();
while ($row = mysqli_fetch_assoc($result)) {
  // npr $data['ID'] = 5;
  $data[] = $row;
}

function cmp($a, $b) {
  global $atribut, $smer;
  // 1 --> a > b, -1 --> b > a, 0 --> a == b
  if(is_numeric($a[$atribut])){
    $result = $a[$atribut] > $b[$atribut] ? 1 : -1;
    // preverimo ali sta enaka, če ne ohranimo trenutno vrednost
    $result = $a[$atribut] == $b[$atribut] ? 0 : $result;
  }
  else{
    // ignorira velikost črk
    $result = strcasecmp($a[$atribut], $b[$atribut]);
  }
  return $smer == 'ASC' ? $result : -$result;
}

usort($data, 'cmp');
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
        <th><a href="?atribut=ID&smer=<?php echo $atribut == 'ID' && $smer == 'ASC' ? 'DESC' : 'ASC'; ?>">ID</a></th>
        <th><a href="?atribut=ime&smer=<?php echo $atribut == 'ime' && $smer == 'ASC' ? 'DESC' : 'ASC'; ?>">Ime</a></th>
        <th><a href="?atribut=priimek&smer=<?php echo $atribut == 'priimek' && $smer == 'ASC' ? 'DESC' : 'ASC'; ?>">Priimek</a></th>
        <th><a href="?atribut=naslov&smer=<?php echo $atribut == 'naslov' && $smer == 'ASC' ? 'DESC' : 'ASC'; ?>">Naslov</a></th>
        <th><a href="?atribut=telefon&smer=<?php echo $atribut == 'telefon' && $smer == 'ASC' ? 'DESC' : 'ASC'; ?>">Telefon</a></th>
        <th><a href="?atribut=e_posta&smer=<?php echo $atribut == 'e_posta' && $smer == 'ASC' ? 'DESC' : 'ASC'; ?>">E-pošta</a></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($data as $row){ ?>
            <tr>
                <td><?php echo $row['ID']; ?></td>
                <td><?php echo $row['ime']; ?></td>
                <td><?php echo $row['priimek']; ?></td>
                <td><?php echo $row['naslov']; ?></td>
                <td><?php echo $row['telefon']; ?></td>
                <td><?php echo $row['e_posta']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
  </table>
</div>
<div class="container mt-5 text-center">
  <button id="graf" type="button" class="btn btn-primary" name="graf">Grafični prikaz</button>
</div>
<div class="container mt-3 mb-3 text-center">
  <button id="nazaj" class="btn btn-primary">Nazaj</button>
</div>
<div class="p-3 bg-dark text-white text-center fixed-bottom">
  <a href="viri.php">Viri</a>
</div>
<script type="text/javascript">
  document.getElementById("graf").onclick = function () {
    location.href = "vinogradniki_graf.php";
  };

  document.getElementById("nazaj").onclick = function () {
    location.href = "vinogradniki_menu.php";
  };
</script>
</body>
</html>
