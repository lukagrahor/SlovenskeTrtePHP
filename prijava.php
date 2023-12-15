<?php
// Start the session
session_start();

// Če je uporabnik že prijavljen, ga preusmeri na začetno stran
if (isset($_SESSION['vinogradnik_ID'])) {
  header('Location: index.php');
  exit;
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $uporabnisko_ime = htmlspecialchars($_POST['uporabnisko_ime']);
    $geslo = htmlspecialchars($_POST['geslo']);
    
    // povezava na bazo
    $conn = mysqli_connect('localhost', 'id21675710_trte', 'Trte123&', 'id21675710_slovensketrte');
    
    // Check if the username and password are correct

    $stmt = mysqli_prepare($conn, "SELECT * FROM vinogradnik WHERE uporabnisko_ime=? AND geslo=?");
    mysqli_stmt_bind_param($stmt, "ss", $uporabnisko_ime, $geslo);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // ob pravilnem imenu in geslu
    if (mysqli_num_rows($result) > 0) {
        $uporabnik = mysqli_fetch_assoc($result);
        // seja si zapolni uporabnika
        $_SESSION['vinogradnik_ID'] = $uporabnik['ID'];
        
        // preusmeritev na začetno stran
        header('Location: index.php');
        exit;
     }
    // ob napačnem imenu in geslu
    else {
      $error_message = 'Vnešeno ime in geslo se ne ujemata.';
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
  
<div class="container mt-5">
    <form method="post" class="was-validated">
        <div class="mb-3 mt-3">
            <label for="uporabnisko_ime" class="form-label">Uporabniško ime:</label>
            <input type="text" class="form-control" id="uporabnisko_ime" placeholder="Vnesi uporabniško ime" name="uporabnisko_ime" required>
            <div class="valid-feedback">Veljavno.</div>
            <div class="invalid-feedback">Prosim izpolnite to polje.</div>
        </div>
        <div class="mb-3">
            <label for="geslo" class="form-label">Geslo:</label>
            <input type="password" class="form-control" id="geslo" placeholder="Vnesi geslo" name="geslo" required>
            <div class="valid-feedback">Veljavno.</div>
            <div class="invalid-feedback">Prosim izpolnite to polje.</div>
        </div>
        <div class="form-check mb-3">
            <label class="form-check-label">
            <input class="form-check-input" type="checkbox" name="remember"> Remember me
            </label>
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
    location.href = "index.php";
  };
</script>
</body>
</html>
