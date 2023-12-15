<?php
// začetek seje
session_start();
// če vinogradnik ni prijavljen ga preusmeri na prijavo
if (!isset($_SESSION['vinogradnik_ID'])) {
    header('Location: prijava.php');
    exit;
}
$vinogradnik_ID = $_SESSION['vinogradnik_ID'];
// iz prejšnje strani dobim ID zemljišča
$ID = $_POST['ID'];
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

if(!isset($_SESSION['velikost']) || $_SESSION['velikost'] == ""){
  $stmt = mysqli_prepare($conn, "SELECT velikost, kolicina_trt FROM zemljisce WHERE ID=?");
  mysqli_stmt_bind_param($stmt, "i", $ID);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  // vrstico uporabim za vnašanje obstoječih rezultatov
  while ($row = mysqli_fetch_assoc($result)) {
    $_SESSION['velikost'] = $row['velikost'];
    $_SESSION['kolicina_trt'] = $row['kolicina_trt'];
  }
}

// preveri ali je bilo poslano
if (isset($_POST['submit'])) {
    $velikost = $_POST['velikost'];
    $kolicina_trt = $_POST['kolicina_trt'];
    // ID vinogradnika je od tistega, ki je ustvaril zemljišče
    $vinogradnik_ID = $_SESSION['vinogradnik_ID'];

    $rules = [
      "velikost" => [
          // Le črke in presledki
          "filter" => FILTER_CALLBACK,
          "options" => function ($value) { return (is_numeric($value) && $value > 0 && $value <= 99999999) ? intval($value) : false; }
      ],
      "kolicina_trt" => [
          "filter" => FILTER_CALLBACK,
          "options" => function ($value) { return (is_numeric($value) && $value >= 0 && $value <= 999999) ? intval($value) : false; }
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
    # ob kliknu na gumb pozabi prejšnjo vrednost ID-ja
    # zato jo shranim v hidden input in jo potem spet preberem
    $ID = $_POST['ID'];
    $velikost = $_POST['velikost'];
    $kolicina_trt = $_POST['kolicina_trt'];

    $stmt = mysqli_prepare($conn, "UPDATE zemljisce SET velikost=?, kolicina_trt=? WHERE ID=?");
    mysqli_stmt_bind_param($stmt, "iii", $_POST['velikost'], $_POST['kolicina_trt'], $ID);
    mysqli_stmt_execute($stmt);

    $_SESSION['ID'] = "";
    $_SESSION['velikost'] = "";
    $_SESSION['kolicina_trt'] = "";
    $_SESSION['errors'] = $errors;
    
    header('Location: zemljisca.php');
    exit;
  }
  else{
    $_SESSION['errors'] = $errors;
  }
}
// trenutni podatki o vinogradniku

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
        <input type="hidden" name="ID" value="<?php echo $ID; ?>">
      <div class="mb-3 mt-3">
          <label for="velikost" class="form-label">Velikost:</label>
          <input type="text" class="form-control" id="velikost" value="<?php echo $_SESSION['velikost']; ?>" name="velikost" required>
          <p><u><b><?= $errors["velikost"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <label for="kolicina_trt" class="form-label">Število trt:</label>
          <input type="text" class="form-control" id="kolicina_trt" value="<?php echo $_SESSION['kolicina_trt']; ?>" name="kolicina_trt" required>
          <p><u><b><?= $errors["kolicina_trt"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <button type="submit" class="btn btn-primary" name = "submit">Potrdi</button>
    </form>
    <div class="container mt-3 mb-3 text-center">
      <button id="nazaj" class="btn btn-primary">Nazaj</button>
    </div>
</div>
<div class="p-3 bg-dark text-white text-center fixed-bottom">
  <a href="viri.php">Viri</a>
</div>
<script type="text/javascript">
  document.getElementById("nazaj").onclick = function () {
    location.href = "zemljisca_last.php";
  };
</script>
</body>
</html>