<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel="icon" href="/IndZ/images/logo.png">
    <link rel='stylesheet' href='/IndZ/styles/header.css'>
    <title>Ошибка!</title>
    <?php
    session_start();
    if (!isset($_SESSION['username']))
        header("Location: " . $_SERVER['DOCUMENT_ROOT'] . "/IndZ/");
//    if (!isset($_SESSION['errno']) || !isset($_SESSION['error']))
//        header("Location: ../", true, 303);
    include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnOpen.php');
    ?>
</head>
<body>
<div style="display: flex; flex-wrap: nowrap; flex-direction: row;">
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/header.php');
    ?>
    <div style="display: flex; flex-wrap: wrap; flex-direction: column; margin: 0 auto">
        <div style="padding-top: 200px; max-width: 600px; min-width: 400px; font-family: 'Lora', sans-serif; font-size: 22px; font-style: normal; color: indianred"
        ">
        <p><output><?php echo "Возникала ошибка $_SESSION[errno]: $_SESSION[error]"; ?></output></p>
        <p><output>Пожалуйста, повторите ваш запрос</output></p>
        <p><output>Если ошибка не пропадает, возможно, у Вас отсутствует подключение к БД или она не работает</output></p>
    </div>
</div>
<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnClose.php');
?>
</body>
</html>
