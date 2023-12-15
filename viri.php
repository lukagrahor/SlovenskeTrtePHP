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
    <ul class = "list-group mb-2">
    <li class="list-group-item list-group-item-info"><b>Viri</b></li>
    <li class="list-group-item"><a href="https://www.w3schools.com/bootstrap5/bootstrap_form_validation.php">https://www.w3schools.com/bootstrap5/bootstrap_form_validation.php</a></li>
    <li class="list-group-item"><a href="https://themewagon.com/themes/free-responsive-bootstrap-5-html5-construction-company-website-template-upconsttruction">https://themewagon.com/themes/free-responsive-bootstrap-5-html5-construction-company-website-template-upconsttruction</a></li>
    <li class="list-group-item"><a href="https://www.w3schools.com/bootstrap5/bootstrap_tables.php">https://www.w3schools.com/bootstrap5/bootstrap_tables.php</a></li>
    <li class="list-group-item"><a href="https://www.w3schools.com/bootstrap5/bootstrap_form_select.php">https://www.w3schools.com/bootstrap5/bootstrap_form_select.php</a></li>
    <li class="list-group-item"><a href="https://stackoverflow.com/questions/12383371/refresh-a-page-using-php">https://stackoverflow.com/questions/12383371/refresh-a-page-using-php</a></li>
    <li class="list-group-item"><a href="https://www.w3schools.com/php/func_string_strcasecmp.asp">https://www.w3schools.com/php/func_string_strcasecmp.asp</a></li>
    <li class="list-group-item"><a href="https://www.w3schools.com/ai/ai_chartjs.asp">https://www.w3schools.com/ai/ai_chartjs.asp</a></li>
    <li class="list-group-item"><a href="https://www.w3schools.com/bootstrap5/bootstrap_list_groups.php">https://www.w3schools.com/bootstrap5/bootstrap_list_groups.php</a></li>
    <li class="list-group-item"><a href="https://getbootstrap.com/docs/5.0/utilities/background">https://getbootstrap.com/docs/5.0/utilities/background</a></li>
    </ul>
</div>

<div class="container mt-3 mb-5 text-center">
  <button id="nazaj" class="btn btn-primary">Nazaj</button>
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
