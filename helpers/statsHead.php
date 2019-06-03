<meta charset="UTF-8">
<link rel="icon" href="/IndZ/images/logo.png">
<link rel='stylesheet' href='/IndZ/styles/table.css'>
<?php
session_start();
if (!isset($_SESSION['username']))
    header("Location: " . $_SERVER['DOCUMENT_ROOT'] . "/IndZ/", true);
include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/dbConnOpen.php");
$conn->set_charset('utf8');
?>

