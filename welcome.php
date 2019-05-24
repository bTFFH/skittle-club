<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel='stylesheet' href='/IndZ/styles/table.css'>
    <title>Игроки клуба</title>
    <?php
    session_start();
    $username = $_SESSION['username'];
    include_once($_SERVER['DOCUMENT_ROOT'].'/IndZ/helpers/dbConnOpen.php');
    ?>
</head>
<body>
<div style="display: flex">
    <div style="flex-wrap: nowrap; margin-left: auto; margin-right: auto">
<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/IndZ/helpers/header.php');
?>
    </div>
</div>
</body>
