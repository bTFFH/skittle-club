<?php
session_start();
if (isset($_SESSION['username'])) header("Location: players.php", true, 303);
else header("Location: ../", true, 303);
?>
