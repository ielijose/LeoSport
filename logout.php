<?php
  session_start();
  unset($_SESSION["LS_admin"]); 
  unset($_SESSION["LS_admin"]);
  session_destroy();
  header("Location: login.php?account");
  exit;
?>