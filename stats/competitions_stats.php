<!DOCTYPE html>
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/statsHead.php'); ?>
    <title>Информация по играм</title>

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
            <caption>Информация по играм</caption>
            <tr>
                <th>Команда</th>
                <th>Игрок</th>
                <th>Противник</th>
                <th>Коли-во кеглей</th>
                <th>Дата</th>
            </tr>
            <?php
            $query = 'SELECT * FROM Competitions_infoV';
            $stmt = $conn->stmt_init();
            if ($stmt->prepare($query)) {
                if ($stmt->execute()) {
                    $stmt->bind_result($id, $team, $player, $opponent, $amount, $gameDate);

                    while ($stmt->fetch()) {
                        $gameDate = substr($gameDate, 8, 2) . "/" . substr($gameDate, 5, 2) . "/" . substr($gameDate, 0, 4);
                        echo "<tr><td>$team</td><td>$player</td><td>$opponent</td><td>$amount</td><td>$gameDate</td></tr>";
                    }

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
