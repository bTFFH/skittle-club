<!DOCTYPE html>
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/statsHead.php'); ?>
    <title>Статистика команд</title>
</head>
<body>
<div style="display: flex">
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/header.php");
    ?>
    <div class="general-table">
        <br/>
        <br/>
        <table cellpadding="3 5">
            <caption>Статистика команд</caption>
            <tr>
                <th>Команда</th>
                <th>Капитан</th>
                <th>Общее число очков</th>
                <th>Число очков за последнюю неделю</th>
                <th>Общее число игр</th>
                <th>Эффективность</th>
            </tr>
            <?php
            $query = 'SELECT * FROM Teams_statsV';
            $stmt = $conn->stmt_init();
            if ($stmt->prepare($query)) {
                if ($stmt->execute()) {
                    $stmt->bind_result($id, $team, $captain, $pointsAmount, $pointsWeekAmount, $gamesAmount, $effectiviness);

                    while ($stmt->fetch())
                        echo "<tr><td>$team</td><td>$captain</td><td>$pointsAmount</td><td>$pointsWeekAmount</td><td>$gamesAmount</td><td>$effectiviness</td></tr>";
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
