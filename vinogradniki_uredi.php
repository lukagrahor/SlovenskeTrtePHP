<?php
// začetek seje
session_start();
// če vinogradnik ni prijavljen ga preusmeri na prijavo
if (!isset($_SESSION['vinogradnik_ID'])) {
  header('Location: prijava.php');
  exit;
}
// povezava na bazo
$conn = mysqli_connect('localhost', 'id21675710_trte', 'Trte123&', 'id21675710_slovensketrte');

$vinogradnik_ID = $_SESSION['vinogradnik_ID'];

if (empty($data)) {
  $data = [
      "ime" => "",
      "priimek" => "",
      "naslov" => "",
      "telefon" => 0,
      "e_posta" => "",
      "uporabnisko_ime" => ""
  ];
}

if (empty($errors)) {
  foreach ($data as $key => $value) {
      $errors[$key] = "";
  }
}

// če en ni nastavljen, ni nobeden
if(!isset($_SESSION['ime']) || $_SESSION['ime'] == ""){
  $stmt = mysqli_prepare($conn, "SELECT ime, priimek, naslov, telefon, e_posta, uporabnisko_ime FROM vinogradnik WHERE ID=?");
  mysqli_stmt_bind_param($stmt, "i", $vinogradnik_ID);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $_SESSION['error_message'] = "";
  while ($row = mysqli_fetch_assoc($result)) {
    $_SESSION['ime'] = $row['ime'];
    $_SESSION['priimek'] = $row['priimek'];
    $_SESSION['naslov'] = $row['naslov'];
    $_SESSION['telefon'] = $row['telefon'];
    $_SESSION['e_posta'] = $row['e_posta'];
    $_SESSION['uporabnisko_ime'] = $row['uporabnisko_ime'];
    $_SESSION['errors'] = $errors;
  }
}

if (isset($_POST['submit'])) {
  # ob kliknu na gumb pozabi prejšnjo vrednost ID-ja
  # zato jo shranim v hidden input in jo potem spet preberem
  $ime = $_POST['ime'];
  $priimek = $_POST['priimek'];
  $naslov = $_POST['naslov'];
  $telefon = $_POST['telefon'];
  $e_posta = $_POST['e_posta'];
  $uporabnisko_ime = $_POST['uporabnisko_ime'];

  $rules = [
    "ime" => [
      // Le črke in presledki
      "filter" => FILTER_VALIDATE_REGEXP,
      "options" => ["regexp" => "/^[ a-zA-Z]+$/"]
    ],
    "priimek" => [
      "filter" => FILTER_VALIDATE_REGEXP,
      "options" => ["regexp" => "/^[ a-zA-Z]+$/"]
    ],
      "naslov" => FILTER_SANITIZE_SPECIAL_CHARS,

      "telefon" => [
        "filter" => FILTER_CALLBACK,
        "options" => function ($value) { return (((strlen((string)$value) == 8)||(strlen((string)$value) == 9))&& $value > 0) ? intval($value) : false; }
    ],
    "e_posta" => [
      "filter" => FILTER_CALLBACK,
      "options" => function ($value) { return (strpos($value, "@") !== false) ? htmlspecialchars($value) : false; }
    ],
    "uporabnisko_ime" => [
      "filter" => FILTER_VALIDATE_REGEXP,
      "options" => ["regexp" => "/^[ a-zA-Z0-9]+$/"]
    ]
  ];
  
  $data = filter_input_array(INPUT_POST, $rules);
  $errors["ime"] = $data["ime"] === false ? "Dovoljene so le črke in presledki." : "";
  // ker bomo stran osvežili si zapomnemo pravilne vnose
  if($data["ime"] !== false){
    $_SESSION['ime'] = $ime;
  }
  else{
    $_SESSION['ime'] = "";
  }

  $errors["priimek"] = $data["priimek"] === false ? "Količina pridelka ne sme biti negativno število." : "";
  if($data["priimek"] !== false){
    $_SESSION['priimek'] = $priimek;
  }
  else{
    $_SESSION['priimek'] = "";
  }

  $errors["naslov"] = $data["naslov"] === false ? "Količina prodanega pridelka ne sme biti negativno število" : "";
  if($data["naslov"] !== false){
    $_SESSION['naslov'] = $naslov;
  }
  else{
    $_SESSION['naslov'] = "";
  }

  $errors["telefon"] = $data["telefon"] === false ? "telefonska mora biti 8 ali 9 mestno število." : "";
  if($data["telefon"] !== false){
    $_SESSION['telefon'] = $telefon;
  }
  else{
    $_SESSION['telefon'] = "";
  }

  $errors["e_posta"] = $data["e_posta"] === false ? "Poštni naslov mora vsebovati @." : "";
  if($data["e_posta"] !== false){
    $_SESSION['e_posta'] = $e_posta;
  }
  else{
    $_SESSION['e_posta'] = "";
  }

  $errors["uporabnisko_ime"] = $data["uporabnisko_ime"] === false ? "Uporabniško ime lahko vsebuje le črke, številke in presledke." : "";
  if($data["uporabnisko_ime"] !== false){
    $_SESSION['uporabnisko_ime'] = $uporabnisko_ime;
  }
  else{
    $_SESSION['uporabnisko_ime'] = "";
  }

  $isDataValid = true;
  foreach ($errors as $error) {
    $isDataValid = $isDataValid && empty($error);
  }
  if ($isDataValid) {
    $ime = $_POST['ime'];
    $priimek = $_POST['priimek'];
    $naslov = $_POST['naslov'];
    $telefon = $_POST['telefon'];
    $e_posta = $_POST['e_posta'];
    $uporabnisko_ime = $_POST['uporabnisko_ime'];
    // preverimo ali je vpisano geslo pravilno
    $stmt = mysqli_prepare($conn, "SELECT * FROM vinogradnik WHERE ID=? AND geslo=?");
    mysqli_stmt_bind_param($stmt, "is", $vinogradnik_ID, $_POST['geslo']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    // ob pravilnem geslu
    if (mysqli_num_rows($result) > 0) {
      $stmt = mysqli_prepare($conn, "UPDATE vinogradnik SET ime=?, priimek=?, naslov=?, telefon=?, e_posta=?, uporabnisko_ime=? WHERE ID=?");
      mysqli_stmt_bind_param($stmt, "sssissi", $_POST['ime'], $_POST['priimek'], $_POST['naslov'], $_POST['telefon'], $_POST['e_posta'], $_POST['uporabnisko_ime'], $vinogradnik_ID);
      mysqli_stmt_execute($stmt);
      $_SESSION['error_message'] = "";
      $_SESSION['ime'] = "";
      // preusmeritev na začetno stran
      header('Location: vinogradniki.php');
      exit;
    }
    // ob napačnem imenu in geslu
    else {
      $_SESSION['error_message'] = "Geslo ni pravilno.";
    }
  }
}
$stmt = mysqli_prepare($conn, "SELECT ime, priimek, naslov, telefon, e_posta, uporabnisko_ime FROM vinogradnik WHERE ID=?");
mysqli_stmt_bind_param($stmt, "i", $vinogradnik_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
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
          <label for="text" class="form-label">Ime:</label>
          <input type="text" class="form-control" id="ime" value="<?php echo $_SESSION['ime']; ?>" name="ime" required>
          <p><u><b><?= $errors["ime"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <label for="text" class="form-label">Priimek:</label>
          <input type="text" class="form-control" id="priimek" value="<?php echo $_SESSION['priimek']; ?>" name="priimek" required>
          <p><u><b><?= $errors["priimek"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <label for="text" class="form-label">Naslov:</label>
          <input type="text" class="form-control" id="naslov" value="<?php echo $_SESSION['naslov']; ?>" name="naslov" required>
          <p><u><b><?= $errors["naslov"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <label for="text" class="form-label">Telefon:</label>
          <input type="tel" class="form-control" id="telefon" value="<?php echo $_SESSION['telefon']; ?>" name="telefon" required>
          <p><u><b><?= $errors["telefon"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <label for="e_posta" class="form-label">Email:</label>
          <input type="email" class="form-control" id="e_posta" value="<?php echo $_SESSION['e_posta']; ?>" name="e_posta" required>
          <p><u><b><?= $errors["e_posta"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <label for="text" class="form-label">Uporabniško ime:</label>
          <input type="text" class="form-control" id="uporabnisko_ime" value="<?php echo $_SESSION['uporabnisko_ime']; ?>" name="uporabnisko_ime" required>
          <p><u><b><?= $errors["uporabnisko_ime"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3">
          <label for="geslo" class="form-label">Geslo:</label>
          <input type="password" class="form-control" id="geslo" placeholder="Vnesi trenutno geslo" name="geslo" required>
          <p><u><b><?= $_SESSION['error_message'] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <button type="submit" class="btn btn-primary" name = "submit">Potrdi</button>
    </form>
    <div class="container mt-3 mb-5 text-center">
      <button id="nazaj" class="btn btn-primary">Nazaj</button>
    </div>
    <div class="container mt-3 mb-3 text-center">
        skrito
    </div>
</div>

<div class="p-3 bg-dark text-white text-center fixed-bottom">
  <a href="viri.php">Viri</a>
</div>
<script type="text/javascript">
  document.getElementById("nazaj").onclick = function () {
    location.href = "vinogradniki_menu.php";
  };
</script>
</body>
</html>