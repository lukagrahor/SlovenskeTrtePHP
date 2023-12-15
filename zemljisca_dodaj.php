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

if (empty($data)) {
  $data = [
      "velikost" => "",
      "kolicina_trt" => "",
  ];
}

if (empty($errors)) {
  foreach ($data as $key => $value) {
      $errors[$key] = "";
  }
}

// če en ni nastavljen, ni nobeden
if(!isset($_SESSION['velikost']) || $_SESSION['velikost'] == ""){
  $_SESSION['velikost'] = "";
  $_SESSION['kolicina_trt'] = "";
}

// preveri ali je bilo poslano
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $velikost = $_POST['velikost'];
    $kolicina_trt = $_POST['kolicina_trt'];
    // ID vinogradnika je od tistega, ki je ustvaril zemljišče
    $vinogradnik_ID = $_SESSION['vinogradnik_ID'];

    $rules = [
      "velikost" => [
          // Le črke in presledki
          "filter" => FILTER_CALLBACK,
          "options" => function ($value) { return (is_numeric($value) && $value > 0 && $value <= 99999999) ? floatval($value) : false; }
      ],
      "kolicina_trt" => [
          "filter" => FILTER_CALLBACK,
          "options" => function ($value) { return (is_numeric($value) && $value >= 0 && $value <= 999999) ? floatval($value) : false; }
      ]
    ];
  // Apply filter to all POST variables; from here onwards we never
  // access $_POST directly, but use the $data array
  $data = filter_input_array(INPUT_POST, $rules);
  
  $errors["velikost"] = $data["velikost"] === false ? "Velikost ne sme biti negativno število ali pa preveliko." : "";
  // ker bomo stran osvežili si zapomnemo pravilne vnose
  if($data["velikost"] !== false){
    $_SESSION['velikost'] = $velikost;
  }
  else{
    $_SESSION['velikost'] = "";
  }

  $errors["kolicina_trt"] = $data["kolicina_trt"] === false ? "Količina trt ne sme biti negativno število ali pa preveliko." : "";
  if($data["kolicina_trt"] !== false){
    $_SESSION['kolicina_trt'] = $kolicina_trt;
  }
  else{
    $_SESSION['kolicina_trt'] = "";
  }
  // Is there an error?
  $isDataValid = true;
  foreach ($errors as $error) {
      $isDataValid = $isDataValid && empty($error);
  }

  if ($isDataValid) {
    $stmt = mysqli_prepare($conn, "INSERT INTO zemljisce (velikost, kolicina_trt, vinogradnik_ID) VALUES (?,?,?)");
    mysqli_stmt_bind_param($stmt, "iii", $_POST['velikost'], $_POST['kolicina_trt'], $vinogradnik_ID);
    mysqli_stmt_execute($stmt);

    $_SESSION['velikost'] = "";
    $_SESSION['kolicina_trt'] = "";
    
    // preusmeritev
    header('Location: zemljisca.php');
    exit;
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
          <label for="ime" class="form-label">Velikost:</label>
          <input type="text" class="form-control" id="ime" value="<?= $_SESSION['velikost'] ?>" name="velikost" required>
          <p><u><b><?= $errors["velikost"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <label for="kolicina_trt" class="form-label">Število trt:</label>
          <input type="text" class="form-control" id="kolicina_trt" value="<?= $_SESSION['kolicina_trt'] ?>" name="kolicina_trt" required>
          <p><u><b><?= $errors["kolicina_trt"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <button type="submit" class="btn btn-primary">Submit</button>
      <button id="nazaj" class="btn btn-primary">Nazaj</button>
    </form>
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