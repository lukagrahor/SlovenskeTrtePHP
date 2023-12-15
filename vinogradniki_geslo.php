<?php
// začetek seje
session_start();

// če je vinogradnik že prijavljen
if (!isset($_SESSION['vinogradnik_ID'])) {
    header('Location: prijava.php');
    exit;
}

// povezava na bazo
$conn = mysqli_connect('localhost', 'root', '', 'slovensketrte');
$vinogradnik_ID = $_SESSION['vinogradnik_ID'];

if (empty($data)) {
    $data = [
        "novo_geslo" => ""
    ];
  }

if (empty($errors)) {
  foreach ($data as $key => $value) {
      $errors[$key] = "";
  }
}

// ti se vedno ponastavijo
$geslo = "";
$uporabnisko_ime = "";
$novo_geslo = "";
// ta se ponastavi le, če je $_SESSION['errors'] prazen
if(!isset($_SESSION['error_message']) || $_SESSION['error_message'] == ""){
  $_SESSION['errors'] = $errors;
  $_SESSION['error_message'] ="";
}
if (isset($_POST['submit'])) {
  # ob kliknu na gumb pozabi prejšnjo vrednost ID-ja
  # zato jo shranim v hidden input in jo potem spet preberem
  $uporabnisko_ime = $_POST['uporabnisko_ime'];
  $geslo = $_POST['geslo'];
  $novo_geslo = $_POST['novo_geslo'];

  $rules = [
    "novo_geslo" => [
      "filter" => FILTER_CALLBACK,
      "options" => function ($value) { return ((preg_match("/^[a-zA-Z0-9]+$/", $value) > 0) && (strlen((string)$value) > 8) && (strlen((string)$value) < 30)) ? htmlspecialchars($value) : false; }
    ]
  ];
  
  $data = filter_input_array(INPUT_POST, $rules);

  $errors["novo_geslo"] = $data["novo_geslo"] === false ? "novo_geslo lahko vsebuje le črke in številke. Poleg tega mora biti daljše od 8 in krajše od 30 zankov" : "";
  if($data["novo_geslo"] === false){
    $novo_geslo = "";
  }
  $isDataValid = true;
  foreach ($errors as $error) {
    $isDataValid = $isDataValid && empty($error);
  }
  if ($isDataValid) {
    // preverimo ali je trenutno geslo pravilno
    $stmt = mysqli_prepare($conn, "SELECT * FROM vinogradnik WHERE uporabnisko_ime=? AND geslo = ? AND ID = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $uporabnisko_ime, $geslo, $vinogradnik_ID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    // če je geslo pravilno
    if (mysqli_num_rows($result) > 0) {
      $stmt = mysqli_prepare($conn, "UPDATE vinogradnik SET geslo=? WHERE ID=?");
      mysqli_stmt_bind_param($stmt, "si", $novo_geslo, $vinogradnik_ID);
      mysqli_stmt_execute($stmt);
      $_SESSION['error_message'] = "";
      $_SESSION['errors'] = "";
      header('Location: index.php');
      exit;
    }
    else {
      $_SESSION['error_message'] = "Uporabniško ime ali geslo ni pravilno";
      $_SESSION['errors'] = $errors;
      // preusmeritev
    }
  }
  else{
    $_SESSION['errors'] = $errors;
  }
}
if (isset($_POST['izbrisi'])) {
    $uporabnisko_ime = $_POST['uporabnisko_ime'];
    $geslo = $_POST['geslo'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM vinogradnik WHERE uporabnisko_ime=? AND geslo = ? AND ID = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $uporabnisko_ime, $geslo, $vinogradnik_ID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    // če je geslo pravilno
    if (mysqli_num_rows($result) > 0) {
      $message = 'Pozor, po brisanju računa, tega ne bo več mogoče povrniti! Izbrisana bodo tudi vsa vaša zemljišča in pridelki';
      $alert_class = 'alert-danger';
    }
    else {
      $_SESSION['error_message'] = "Uporabniško ime ali geslo ni pravilno";
      $_SESSION['errors'] = $errors;
      // preusmeritev
    }
}
// po končni potrditvi izbrišemo vsa zemljišča, pridelke in samega uporabnika
if (isset($_POST['potrdi_izbris'])) {
    // dobimo vsa zemljišča in vsakemu izbrišemo pridelke
    $stmt = mysqli_prepare($conn, "SELECT ID FROM zemljisce WHERE ID = ?");
    mysqli_stmt_bind_param($stmt, "i", $vinogradnik_ID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $stmt = mysqli_prepare($conn, "DELETE FROM pridelek WHERE zemljisce_ID = ?");
        mysqli_stmt_bind_param($stmt, "i", $row['ID']);
        mysqli_stmt_execute($stmt);
    }

    // izbrišemo še vsa zemljišča in samega vinogradnika
    $stmt = mysqli_prepare($conn, "DELETE FROM zemljisce WHERE vinogradnik_ID = ?");
    mysqli_stmt_bind_param($stmt, "i", $vinogradnik_ID);
    mysqli_stmt_execute($stmt);

    $stmt = mysqli_prepare($conn, "DELETE FROM vinogradnik WHERE ID = ?");
    mysqli_stmt_bind_param($stmt, "i", $vinogradnik_ID);
    mysqli_stmt_execute($stmt);
    
    $_SESSION['error_message'] = "";
    $_SESSION['errors'] = "";
    unset($_SESSION['vinogradnik_ID']);
    session_destroy();
    header('Location: index.php');
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
        <?php if (isset($message)): ?>
            <div class="alert <?php echo $alert_class; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="submit" class="btn btn-primary" name = "potrdi_izbris">Razumem</button>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </form>
            </div>
        <?php endif; ?>
      <div class="mb-3 mt-3">
          <label for="uporabnisko_ime" class="form-label">uporabniško ime:</label>
          <input type="text" class="form-control" id="uporabnisko_ime" placeholder="Vnesite svoje uporabniško ime" name="uporabnisko_ime" required>
          <p><u><b><?= $_SESSION['error_message'] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3">
          <label for="geslo" class="form-label">Trenutno geslo:</label>
          <input type="password" class="form-control" id="geslo" placeholder="Vnesite trenutno geslo" name="geslo" required>
          <p><u><b><?= $_SESSION['error_message'] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <div class="mb-3">
          <label for="novo_geslo" class="form-label">Novo geslo:</label>
          <input type="password" class="form-control" id="novo_geslo" placeholder="Vnesite novo geslo" name="novo_geslo" required>
          <p><u><b><?= $errors["novo_geslo"] ?></b></u></p>
          <div class="valid-feedback">Veljavno.</div>
          <div class="invalid-feedback">Prosim izpolnite to polje.</div>
      </div>
      <button type="submit" class="btn btn-primary" name = "submit">Potrdi</button>
      <button type="submit" class="btn btn-primary" name = "izbrisi">Izbriši račun</button>
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
    location.href = "vinogradniki_uredi.php";
  };
</script>
</body>
</html>