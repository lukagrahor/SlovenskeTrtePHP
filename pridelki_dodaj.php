<?php
// začetek seje
session_start();
// če vinogradnik ni še prijavlejn
if (!isset($_SESSION['vinogradnik_ID'])) {
    header('Location: prijava.php');
    exit;
}

// povezava na bazo
$conn = mysqli_connect('localhost', 'id21675710_trte', 'Trte123&', 'id21675710_slovensketrte');

// If $errors array is empty, let's make it contain the same keys as
// $data array, but with empty values

if (empty($data)) {
  $data = [
      "vrsta" => "",
      "kolicina_pridelka" => "",
      "kolicina_prodanega_pridelka" => "",
      "cena" => 0,
  ];
}

if (empty($errors)) {
  foreach ($data as $key => $value) {
      $errors[$key] = "";
  }
}

// če en ni nastavljen, ni nobeden
if(!isset($_SESSION['vrsta']) || $_SESSION['vrsta'] == ""){
  $_SESSION['vrsta'] = "";
  $_SESSION['kolicina_pridelka'] = "";
  $_SESSION['kolicina_prodanega_pridelka'] = "";
  $_SESSION['cena'] = "";
  $_SESSION['errors'] = $errors;
}

// preveri ali je bilo poslano
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $vrsta = $_POST['vrsta'];
  $kolicina_pridelka = $_POST['kolicina_pridelka'];
  $kolicina_prodanega_pridelka = $_POST['kolicina_prodanega_pridelka'];
  $cena = $_POST['cena'];
  $zemljisce_ID = $_POST['zemljisce_ID'];

  $rules = [
    "vrsta" => [
        // Le črke in presledki
        "filter" => FILTER_VALIDATE_REGEXP,
        "options" => ["regexp" => "/^[ a-zA-Z]+$/"]
    ],
    "kolicina_pridelka" => [
        "filter" => FILTER_CALLBACK,
        "options" => function ($value) { return (is_numeric($value) && $value >= 0) ? floatval($value) : false; }
    ],
    "kolicina_prodanega_pridelka" => [
      "filter" => FILTER_CALLBACK,
      "options" => function ($value) { return (is_numeric($value) && $value >= 0) ? floatval($value) : false; }
  ],
    "cena" => [
        "filter" => FILTER_CALLBACK,
        "options" => function ($value) { return (is_numeric($value) && $value >= 0 && $value <= 100000) ? floatval($value) : false; }
  ]
  ];
  // Apply filter to all POST variables; from here onwards we never
  // access $_POST directly, but use the $data array
  $data = filter_input_array(INPUT_POST, $rules);
  
  $errors["vrsta"] = $data["vrsta"] === false ? "Dovoljene so le črke in presledki." : "";
  // ker bomo stran osvežili si zapomnemo pravilne vnose
  if($data["vrsta"] !== false){
    $_SESSION['vrsta'] = $vrsta;
  }
  else{
    $_SESSION['vrsta'] = "";
  }

  $errors["kolicina_pridelka"] = $data["kolicina_pridelka"] === false ? "Količina pridelka ne sme biti negativno število." : "";
  if($data["kolicina_pridelka"] !== false){
    $_SESSION['kolicina_pridelka'] = $kolicina_pridelka;
  }
  else{
    $_SESSION['kolicina_pridelka'] = "";
  }

  $errors["kolicina_prodanega_pridelka"] = $data["kolicina_prodanega_pridelka"] === false ? "Količina prodanega pridelka ne sme biti negativno število" : "";
  if($data["kolicina_prodanega_pridelka"] !== false){
    $_SESSION['kolicina_prodanega_pridelka'] = $kolicina_prodanega_pridelka;
  }
  else{
    $_SESSION['kolicina_prodanega_pridelka'] = "";
  }

  $errors["cena"] = $data["cena"] === false ? "Cena ne sme biti negativno število ali večje od 100000." : "";
  if($data["cena"] !== false){
    $_SESSION['cena'] = $cena;
  }
  else{
    $_SESSION['cena'] = "";
  }
  // Is there an error?
  $isDataValid = true;
  foreach ($errors as $error) {
      $isDataValid = $isDataValid && empty($error);
  }

  if ($isDataValid) {
      $stmt = mysqli_prepare($conn, "INSERT INTO pridelek (vrsta, kolicina_pridelka, kolicina_prodanega_pridelka, cena, zemljisce_ID) VALUES (?, ?, ?, ?, ?)");
      mysqli_stmt_bind_param($stmt, "siiii", $_POST['vrsta'], $_POST['kolicina_pridelka'], $_POST['kolicina_prodanega_pridelka'], $_POST['cena'], $_POST['zemljisce_ID']);
      mysqli_stmt_execute($stmt);
      // ponastavi vse, da se ne bo zapolnil za naslednjič, ko bo želel vinogradnik dodati pridelek
      $_SESSION['vrsta'] = "";
      $_SESSION['kolicina_pridelka'] = "";
      $_SESSION['kolicina_prodanega_pridelka'] = "";
      $_SESSION['cena'] = "";
      $_SESSION['errors'] = $errors;
      // preusmeritev
      header('Location: pridelki.php');
      exit;
  }
  else {
    $_SESSION['errors'] = $errors;
    #header("Refresh:0");
  }
}
//seznam zemljišč je le tistih, ki pripadajo prijavljenem vinogradniku
$vinogradnik_ID = $_SESSION['vinogradnik_ID'];
$stmt = mysqli_prepare($conn, "SELECT ID FROM zemljisce WHERE vinogradnik_ID = ?");
mysqli_stmt_bind_param($stmt, "s", $vinogradnik_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
// v vsaki vrstici bo 1 ID zemljišča
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
<!-- navbar-dark = bela pisava -->
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
<!-- Vinogradnik ob prijavi doda svoje zemljišče in pridelek -->
<div class="container mt-5">
    <form method="post" class="was-validated">
      <div class="mb-3 mt-3">
          <label for="vrsta" class="form-label">Vrsta:</label>
          <input type="text" class="form-control" id="vrsta" value="<?= $_SESSION['vrsta'] ?>" name="vrsta" required>
          <p><u><b><?= $errors["vrsta"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <label for="kolicina_pridelka" class="form-label">Količina pridelka:</label>
          <input type="text" class="form-control" id="kolicina_pridelka" value="<?php echo $_SESSION['kolicina_pridelka'] ?>" name="kolicina_pridelka" required>
          <p><u><b><?= $errors["kolicina_pridelka"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <label for="kolicina_prodanega_pridelka" class="form-label">Količina prodanega pridelka:</label>
          <input type="text" class="form-control" id="kolicina_prodanega_pridelka" value="<?= $_SESSION['kolicina_prodanega_pridelka'] ?>" name="kolicina_prodanega_pridelka" required>
          <p><u><b><?= $errors["kolicina_prodanega_pridelka"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <label for="cena" class="form-label">Cena:</label>
          <input type="text" class="form-control" id="cena" value="<?= $_SESSION['cena'] ?>" name="cena" required>
          <p><u><b><?= $errors["cena"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <label for="zemljisce_ID" class="form-label">ID zemljišča:</label>
          <select class="form-select" id="zemljisce_ID" name="zemljisce_ID">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
              <option><?php echo $row['ID']; ?></option>
            <?php endwhile; ?>
          </select>
      </div>
      <button type="submit" class="btn btn-primary" onclick="napaka()">Submit</button>
      <button id="nazaj" class="btn btn-primary">Nazaj</button>
    </form>
</div>
<div class="p-3 bg-dark text-white text-center fixed-bottom">
  <a href="viri.php">Viri</a>
</div>
<script>
  document.getElementById("nazaj").onclick = function () {
    location.href = "pridelki_menu.php";
  };
</script>
</body>
</html>