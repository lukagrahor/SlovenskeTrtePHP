<?php
// začetek seje
session_start();
// če vinogradnik ni prijavljen ga preusmeri na prijavo
if (!isset($_SESSION['vinogradnik_ID'])) {
    header('Location: prijava.php');
    exit;
}

// povezava na bazo
$conn = mysqli_connect('localhost', 'root', '', 'slovensketrte');

$vinogradnik_ID = $_SESSION['vinogradnik_ID'];
// iz prejšnje strani dobim ID pridelka
if(isset($_POST['ID'])){
  $ID = $_POST['ID'];
}
if(isset($_POST['zemljisce_ID'])){
  $zemljisce_ID = $_POST['zemljisce_ID'];
}
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
  $stmt = mysqli_prepare($conn, "SELECT vrsta, kolicina_pridelka, kolicina_prodanega_pridelka, cena, zemljisce_ID FROM pridelek WHERE ID=?");
  mysqli_stmt_bind_param($stmt, "i", $ID);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $_SESSION['ID'] = $ID;
  $_SESSION['zemljisce_ID'] = $zemljisce_ID;
  while ($row = mysqli_fetch_assoc($result)) {
    $_SESSION['vrsta'] = $row['vrsta'];
    $_SESSION['kolicina_pridelka'] = $row['kolicina_pridelka'];
    $_SESSION['kolicina_prodanega_pridelka'] = $row['kolicina_prodanega_pridelka'];
    $_SESSION['cena'] = $row['cena'];
    $_SESSION['errors'] = $errors;
  }
}

if (isset($_POST['submit'])) {
    # ob kliknu na gumb pozabi prejšnjo vrednost ID-ja
    # zato jo shranim v hidden input in jo potem spet preberem
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
      $stmt = mysqli_prepare($conn, "UPDATE pridelek SET vrsta=?, kolicina_pridelka=?, kolicina_prodanega_pridelka=?, cena = ?, zemljisce_ID=? WHERE ID=?");
      mysqli_stmt_bind_param($stmt, "siiiii", $_POST['vrsta'], $_POST['kolicina_pridelka'], $_POST['kolicina_prodanega_pridelka'], $_POST['cena'], $_POST['zemljisce_ID'], $_SESSION['ID']);
      mysqli_stmt_execute($stmt);

      // ponastavi vse, da se ne bo zapolnil za naslednjič, ko bo želel vinogradnik dodati pridelek
      $_SESSION['ID'] = "";
      $_SESSION['zemljisce_ID']= "";
      $_SESSION['vrsta'] = "";
      $_SESSION['kolicina_pridelka'] = "";
      $_SESSION['kolicina_prodanega_pridelka'] = "";
      $_SESSION['cena'] = "";
      $_SESSION['errors'] = $errors;
    
      header('Location: pridelki.php');
      exit;
    }
    else {
      $_SESSION['errors'] = $errors;
    }
}
$stmt = mysqli_prepare($conn, "SELECT z.ID FROM zemljisce z INNER JOIN vinogradnik v ON v.ID = z.vinogradnik_ID WHERE z.vinogradnik_ID=?");
mysqli_stmt_bind_param($stmt, "i", $vinogradnik_ID);
mysqli_stmt_execute($stmt);
$result2 = mysqli_stmt_get_result($stmt);
// vrstico uporabim za vnašanje obstoječih rezultatov
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
      <input type="hidden" name="ID" value="<?php echo $SESSION['ID']; ?>">
      <div class="mb-3 mt-3">
          <label for="vrsta" class="form-label">Vrsta:</label>
          <input type="text" class="form-control" id="vrsta" value="<?php echo $_SESSION['vrsta']; ?>" name="vrsta" required>
          <p><u><b><?= $errors["vrsta"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <label for="kolicina_pridelka" class="form-label">Količina pridelka(kg):</label>
          <input type="text" class="form-control" id="kolicina_pridelka" value="<?php echo $_SESSION['kolicina_pridelka']; ?>" name="kolicina_pridelka" required>
          <p><u><b><?= $errors["kolicina_pridelka"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <label for="kolicina_prodanega_pridelka" class="form-label">Količina prodanega pridelka(kg):</label>
          <input type="text" class="form-control" id="kolicina_prodanega_pridelka" value="<?php echo $_SESSION['kolicina_prodanega_pridelka']; ?>" name="kolicina_prodanega_pridelka" required>
          <p><u><b><?= $errors["kolicina_prodanega_pridelka"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <label for="cena" class="form-label">Cena na kg:</label>
          <input type="text" class="form-control" id="cena" value="<?php echo $_SESSION['cena']; ?>" name="cena" required>
          <p><u><b><?= $errors["cena"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3 mt-3">
          <select name="zemljisce_ID" id="zemljisce_ID">
              <?php
              // loop through the query results to create option tags
              while ($row = mysqli_fetch_assoc($result2)) {
                // set the selected attribute to the option that matches the selected ID
                $selected = ($SESSION['zemljisce_ID'] == $row['ID']) ? 'selected' : '';
                echo "<option value='{$row['ID']}' $selected>{$row['ID']}</option>";
              }
              ?>
          </select>
      </div>
    <button type="submit" class="btn btn-primary" name = "submit">Potrdi</button>
  </form>
<div class="container mt-3 mb-5 text-center">
  <button id="nazaj" class="btn btn-primary">Nazaj</button>
</div>
<div class="p-3 bg-dark text-white text-center fixed-bottom">
  <a href="viri.php">Viri</a>
</div>
<script type="text/javascript">
  document.getElementById("nazaj").onclick = function () {
    location.href = "pridelki_last.php";
  };
</script>
</body>
</html>