<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel="icon" href="/IndZ/images/logo.png">
    <link rel='stylesheet' href='/IndZ/styles/welcome.css'>
    <link rel='stylesheet' href='/IndZ/styles/header.css'>
    <title>Добро пожаловать!</title>
    <?php
    session_start();
    if (!isset($_SESSION['username']))
        header("Location: index.php", true, 303);
    $username = $_SESSION['username'];
    include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnOpen.php');
    ?>
</head>
<body>
<div style="display: flex">
    <div style="flex-wrap: nowrap;">
        <?php
        include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/header.php');
        ?>
    </div>
    <div class="welcome">
        <p style="margin-top: 200px; max-width: 350px"><output>Добро пожаловать, <?php echo $_SESSION['name']; ?>!</output></p>
        <p style="max-width: 300px"><output>Давай начнем работу в этот прекрасный день с выбора вкладки на боковой панели</output></p>
    </div>
</div>
</body>
