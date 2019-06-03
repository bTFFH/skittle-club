<?php
session_start();
if (isset($_SESSION['username'])) header("Location: player.php", true, 303);
else header("Location: ../", true, 303);
?>
