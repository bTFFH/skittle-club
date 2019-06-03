<?php
session_start();
if (isset($_SESSION['username'])) {
    $_SESSION['errno'] = 403;
    $_SESSION['error'] = "Access restricted";
    header("Location: error.php", true, 303);
}
else header("Location: ../", true, 303);
?>
