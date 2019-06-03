<!DOCTYPE html>
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/statsHead.php'); ?>
    <title>Статистика игроков</title>
</head>
<body>
<div style="display: flex">
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/header.php");
    ?>
    <div class="general-table">
        <br />
        <br />
        <table cellpadding="3 5">
            <caption>Статистика игроков клуба</caption>
            <tr>
                <th>Игрок</th>
                <th>Команда</th>
                <th>Общее число кеглей</th>
                <th>Число кеглей за последнюю неделю</th>
                <th>Общее число игр</th>
                <th>Эффективность</th>
            </tr>
            <?php
            $query = 'SELECT * FROM Players_statsV';
            $stmt = $conn->stmt_init();
            if ($stmt->prepare($query)) {
                if ($stmt->execute()) {
                    $stmt->bind_result($id, $name, $surname, $team, $skAmount, $skWeekAmount, $gamesAmount, $effectiviness);

                    while ($stmt->fetch())
                        echo "<tr><td>$name $surname</td><td>$team</td><td>$skAmount</td><td>$skWeekAmount</td><td>$gamesAmount</td><td>$effectiviness</td></tr>";

                    $stmt->free_result();
                    $stmt->close();
                } else {
                $_SESSION['errno'] = $stmt->errno;
                $_SESSION['error'] = $stmt->error;
                header("Location: ../helpers/error.php", true);
            }
            } else {
                $_SESSION['errno'] = $stmt->errno;
                $_SESSION['error'] = $stmt->error;
                header("Location: ../helpers/error.php", true);
            }
            ?>
        </table>
    </div>
</div>
<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/dbConnClose.php");
?>
</body>
</html>
