<?php
// začetek seje
session_start();

// če je vinogradnik že prijavljen
if (isset($_SESSION['vinogradnik_ID'])) {
  header('Location: index.php');
  exit;
}

// povezava na bazo
$conn = mysqli_connect('localhost', 'root', '', 'slovensketrte');
if (empty($data)) {
  $data = [
      "ime" => "",
      "priimek" => "",
      "naslov" => "",
      "telefon" => 0,
      "e_posta" => "",
      "uporabnisko_ime" => "",
      "geslo" => ""
  ];
}

if (empty($errors)) {
  foreach ($data as $key => $value) {
      $errors[$key] = "";
  }
}

// če en ni nastavljen, ni nobeden
if(!isset($_SESSION['ime']) || $_SESSION['ime'] == ""){
  $_SESSION['error_message'] = "";
  $_SESSION['ime'] = "";
  $_SESSION['priimek'] = "";
  $_SESSION['naslov'] = "";
  $_SESSION['telefon'] = "";
  $_SESSION['e_posta'] = "";
  $_SESSION['uporabnisko_ime'] = "";
  $_SESSION['geslo'] = "";
  $_SESSION['errors'] = $errors;
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
  $geslo = $_POST['geslo'];

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
      "naslov" => [
        "filter" => FILTER_VALIDATE_REGEXP,
        "options" => ["regexp" => "/^[ a-zA-Z0-9]+$/"]
    ],
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
    ],
    "geslo" => [
      "filter" => FILTER_CALLBACK,
      "options" => function ($value) { return ((preg_match("/^[a-zA-Z0-9]+$/", $value) > 0) && (strlen((string)$value) > 8) && (strlen((string)$value) < 30)) ? htmlspecialchars($value) : false; }
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

  $errors["naslov"] = $data["naslov"] === false ? "Naslov lahko vsebuje le črke, številke in presledke" : "";
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

  $errors["geslo"] = $data["geslo"] === false ? "Geslo ime lahko vsebuje le črke in številke. Poleg tega mora biti daljše od 8 in krajše od 30 zankov" : "";
  if($data["geslo"] !== false){
    $_SESSION['geslo'] = $geslo;
  }
  else{
    $_SESSION['geslo'] = "";
  }
  $isDataValid = true;
  foreach ($errors as $error) {
    $isDataValid = $isDataValid && empty($error);
  }
  if ($isDataValid) {
    // preverimo ali enako uporabniško ime že obstaja
    $stmt = mysqli_prepare($conn, "SELECT * FROM vinogradnik WHERE uporabnisko_ime=?");
    mysqli_stmt_bind_param($stmt, "s", $uporabnisko_ime);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) > 0) {
      $_SESSION['error_message'] = "Uporabniško ime že obstaja";
      $_SESSION['errors'] = $errors;
    }
    else {
      $stmt = mysqli_prepare($conn,"INSERT INTO vinogradnik (ime, priimek, naslov, telefon, e_posta, uporabnisko_ime, geslo) VALUES (?, ?, ?, ?, ?, ?, ?)");
      mysqli_stmt_bind_param($stmt, "sssisss", $ime, $priimek, $naslov, $telefon, $e_posta, $uporabnisko_ime, $geslo);
      mysqli_stmt_execute($stmt);
      $_SESSION['ime'] = "";
      $_SESSION['error_message'] = "";
      // preusmeritev
      header('Location: prijava.php');
      exit;
    }
  }
  else{
    $_SESSION['errors'] = $errors;
  }
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

<!-- navbar-dark = bela pisava -->
<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  <div class="container-fluid">
    <ul class="navbar-nav" id = "nav-items">
      <li class="nav-item">
        <a class="nav-link active" href="index.php">
        <img src="Slike/logo.png" alt="Avatar Logo" style="width:37px;" class="rounded-pill">
        </a>
      </li>
    </ul>
  </div>
</nav>

<!-- Vinogradnik ob prijavi doda svoje zemljišče in pridelek -->
<div class="container mt-5">
    <form method="post" class="was-validated">
      <div class="mb-3 mt-3">
          <label for="ime" class="form-label">Ime:</label>
          <input type="text" class="form-control" id="ime" value="<?php echo $_SESSION['ime']; ?>" name="ime" required>
          <p><u><b><?= $_SESSION['error_message'] ?></b></u></p>
          <p><u><b><?= $errors["ime"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <label for="priimek" class="form-label">Priimek:</label>
          <input type="text" class="form-control" id="priimek" value="<?php echo $_SESSION['priimek']; ?>" name="priimek" required>
          <p><u><b><?= $errors["priimek"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <label for="naslov" class="form-label">Naslov:</label>
          <input type="text" class="form-control" id="naslov" value="<?php echo $_SESSION['naslov']; ?>" name="naslov" required>
          <p><u><b><?= $errors["naslov"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <label for="telefon" class="form-label">Telefon:</label>
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
          <label for="uporabnisko_ime" class="form-label">Uporabniško ime:</label>
          <input type="text" class="form-control" id="uporabnisko_ime" value="<?php echo $_SESSION['uporabnisko_ime']; ?>" name="uporabnisko_ime" required>
          <p><u><b><?= $errors["uporabnisko_ime"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3">
          <label for="geslo" class="form-label">Geslo:</label>
          <input type="password" class="form-control" id="geslo" value="<?php echo $_SESSION['geslo']; ?>" name="geslo" required>
          <p><u><b><?= $errors["geslo"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <button type="submit" class="btn btn-primary" name = "submit">Submit</button>
      <button id="nazaj" class="btn btn-primary">Nazaj</button>
    </form>
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