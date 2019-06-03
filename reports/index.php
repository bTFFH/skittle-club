<?php
session_start();
if (isset($_SESSION['username'])) header("Location: playgrounds_usage.php", true, 303);
else header("Location: ../", true, 303);
?>
