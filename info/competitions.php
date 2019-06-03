<!DOCTYPE html>
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/infoHead.php'); ?>
    <title>История игр</title>
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
            <caption>История игр команд клуба</caption>
            <tr>
                <th>Команда 1</th>
                <th>Команда 2</th>
                <th>Игровая площадка</th>
                <th>Дата игры</th>
                <th>Командная явка</th>
                <th></th>
                <th></th>
            </tr>
            <?php
            $query = 'SELECT * FROM CompetitionsV';
            $stmt = $conn->stmt_init();
            if ($stmt->prepare($query) && $stmt->execute()) {
                $stmt->bind_result($id, $team1, $team2, $playground, $gameDate, $absence);

                while ($stmt->fetch()) {
                    $gameDate = substr($gameDate, 8, 2) . '/' . substr($gameDate, 5, 2) . '/' . substr($gameDate, 0, 4);
                    echo "<tr><td>$team1</td><td>$team2</td><td>$playground</td><td>$gameDate</td><td>$absence</td><td class='edit-btn'><form method='POST' action='/IndZ/actions/game.php'><button type='submit' name='edit' value=$id><img src='/IndZ/images/settings.svg' alt='Изменить'/></button></form></td><td class='edit-btn'><form method='POST' action='/IndZ/helpers/delete.php'><button type='submit' name='delete' value=\"$id competitions\"><img src='/IndZ/images/delete.svg' alt='Удалить'/></button></form></td></tr>";
                }
                $stmt->free_result();
                $stmt->close();
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
