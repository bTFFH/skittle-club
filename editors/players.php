<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel='stylesheet' href='/IndZ/styles/table.css'>
    <link rel='stylesheet' href='/IndZ/styles/buttons.css'>
    <title>Игроки клуба</title>
    <?php
    session_start();
    if ( !isset($_SESSION['username']) )
        header("Location: " . $_SERVER['DOCUMENT_ROOT'] . "/IndZ/");
    include_once($_SERVER['DOCUMENT_ROOT'].'/IndZ/helpers/dbConnOpen.php');
    ?>
</head>
<body>
<div style="display: flex; flex-wrap: nowrap; flex-direction: row;">
    <div class="general-table">
        <br />
        <br />
        <table cellpadding="3 5">
            <caption>Игроки клуба</caption>
            <tr>
                <th>Имя</th>
                <th>Фамилия</th>
                <th>Телефон</th>
                <th>Адрес</th>
                <th>Команда</th>
                <th>Empty</th>
            </tr>
<?php
$query = 'SELECT * FROM PlayersV';
$stmt = $conn->stmt_init();



include_once($_SERVER['DOCUMENT_ROOT'].'/IndZ/helpers/dbConnClose.php');



echo $_POST['edit'];
