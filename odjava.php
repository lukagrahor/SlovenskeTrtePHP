<?php
session_start();
unset($_SESSION['vinogradnik_ID']);
session_destroy();
header("Location: index.php"); // redirect to home page
exit;
?>