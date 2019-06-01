<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel='stylesheet' href='/IndZ/styles/table.css'>
    <title>Статистика игр по площадкам</title>
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
    <div class="general-table">
        <br />
        <br />
        <table cellpadding="3 5">
            <caption>Игры по площадкам</caption>
            <tr>
                <th>Название</th>
                <th>Описание, особенности</th>
                <th>Количество игр</th>
            </tr>
            <?php
            $query = 'SELECT * FROM Playgrounds_games_amountV';
            $stmt = $conn->stmt_init();
            if ($stmt->prepare($query)) {
                $stmt->execute();
                $stmt->bind_result($id, $name, $features, $amount);

                while ($stmt->fetch()) {
                    if ($features == '') $features = 'Нет данных';
                    echo "<tr><td>$name</td><td style='max-width: 550px'>$features</td><td>$amount</td></tr>";
                }

                $stmt->free_result();
                $stmt->close();
            }
            ?>
        </table>
    </div>
</div>
<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnClose.php');
?>
</body>
</html>
