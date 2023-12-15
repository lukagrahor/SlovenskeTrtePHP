<?php
// Start the session
session_start();
// povezava na bazo
$conn = mysqli_connect('localhost', 'id21675710_trte', 'Trte123&', 'id21675710_slovensketrte');

$stmt = mysqli_prepare($conn, "SELECT ID, vrsta, kolicina_pridelka, kolicina_prodanega_pridelka, cena, zemljisce_ID FROM pridelek");
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
  // -1 --> a > b, 1 --> b > a, 0 --> a == b
  if(is_numeric($a[$atribut])){
    $result = $a[$atribut] > $b[$atribut] ? -1 : 1;
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
  
<div class="container mt-5">
  <table class="table">
    <thead class="table-dark">
      <tr>
        <th><a href="?atribut=ID&smer=<?php echo $atribut == 'ID' && $smer == 'ASC' ? 'DESC' : 'ASC'; ?>">ID</a></th>
        <th><a href="?atribut=vrsta&smer=<?php echo $atribut == 'vrsta' && $smer == 'ASC' ? 'DESC' : 'ASC'; ?>">Vrsta</a></th>
        <th><a href="?atribut=kolicina_pridelka&smer=<?php echo $atribut == 'kolicina_pridelka' && $smer == 'ASC' ? 'DESC' : 'ASC'; ?>">Količina pridelka(kg)</a></th>
        <th><a href="?atribut=kolicina_prodanega_pridelka&smer=<?php echo $atribut == 'kolicina_prodanega_pridelka' && $smer == 'ASC' ? 'DESC' : 'ASC'; ?>">Količina prodanega pridelka(kg)</a></th>
        <th><a href="?atribut=cena&smer=<?php echo $atribut == 'cena' && $smer == 'ASC' ? 'DESC' : 'ASC'; ?>">Cena na kg</a></th>
        <th><a href="?atribut=zemljisce_ID&smer=<?php echo $atribut == 'zemljisce_ID' && $smer == 'ASC' ? 'DESC' : 'ASC'; ?>">ID zemljišča</a></th>
        <th>Podrobnosti</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($data as $row){ ?>
            <tr>
                <td><?php echo $row['ID']; ?></td>
                <td><?php echo $row['vrsta']; ?></td>
                <td><?php echo $row['kolicina_pridelka']; ?></td>
                <td><?php echo $row['kolicina_prodanega_pridelka']; ?></td>
                <td><?php echo $row['cena']; ?></td>
                <td><?php echo $row['zemljisce_ID']; ?></td>
                <td>
                    <form method="POST" action="pridelki_podrobnosti.php">
                        <input type="hidden" name="ID" value="<?php echo $row['ID']; ?>">
                        <input type="hidden" name="zemljisce_ID" value="<?php echo $row['zemljisce_ID']; ?>">
                        <input type="submit" class="btn btn-primary" name="uredi" value="Prikazi vec">
                    </form>
                </td>
            </tr>
        <?php }?>
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
    location.href = "pridelki_graf.php";
  };

  document.getElementById("nazaj").onclick = function () {
    location.href = "pridelki_menu.php";
  };
</script>
</body>
</html>
