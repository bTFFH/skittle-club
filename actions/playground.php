<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel='stylesheet' href='/IndZ/styles/addition.css'>
    <link rel='stylesheet' href='/IndZ/styles/buttons.css'>
    <title>Добавление площадки</title>
    <?php
    session_start();
    if (!isset($_SESSION['username']))
        header("Location: " . $_SERVER['DOCUMENT_ROOT'] . "/IndZ/");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnOpen.php');
    ?>
</head>
<body>
<div style="display: flex">
    <div style="flex-wrap: nowrap">
        <?php
        include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/header.php');
        ?>
    </div>
    <div class="add-form" style="flex-basis: 500px">
        <br/>
        <br/>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $query = 'INSERT INTO `playgrounds`(`name`, `features`) VALUES (?, ?)';
            $stmt = $conn->stmt_init();
            if ($stmt->prepare($query)) {
                $stmt->bind_param('ss', $_POST['name'], $_POST['features']);
                if ($stmt->execute()) {
                    echo "<p><output style='color: seagreen;'>Новая площадка успешно добавлена</output></p>";
                    $stmt->close();
                } else {
                    $stmt->close();
                    ?>
                    <form name="errorNewPlayground" method="GET" action="playground.php">
                        <p><output>При добавлении площадки возникла ошибка</output></p>
                        <p><output>Попробойте осуществить операцию еще раз</output></p>
                        <div class="submit-btn"><input type="submit" value="Попробовать"/></div>
                    </form>
                    <?php
                }
            }
        } else {
            ?>
            <form name="insertNewPlayground" method="POST" action="playground.php">
                <p><label>Название площадки<input type="text" name="name" maxlength="50" pattern="[0-9A-ZА-Я^ЪЬ]{1}.*"
                                                  placeholder="Underground"/></label></p>
                <p><label>Особенности<textarea name="features" maxlength="255" wrap="soft"
                                               placeholder="Ambience of the UK underground"></textarea></label></p>
                <div class="submit-btn" style="margin-left: 7px"><input type="submit" value="Добавить"/></div>
            </form>
            <?php
        }
        ?>
    </div>

</div>
<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnClose.php');
?>
</body>
</html>
