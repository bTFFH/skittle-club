<?php
session_start();

unset($_SESSION['username']);
header("Location: /IndZ/index.php");
//exit("I am working");
//session_start();
//session_destroy();
//header("Location: ".$_SERVER['DOCUMENT_ROOT']."/IndZ/index.php");
?>
