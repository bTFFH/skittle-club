<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel='stylesheet' href='/IndZ/styles/table.css'>
    <title>Информация по играм</title>
    <?php
    session_start();
    if (!isset($_SESSION['username']))
        header("Location: " . $_SERVER['DOCUMENT_ROOT'] . "/IndZ/");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/dbConnOpen.php");
    ?>
</head>
<body>
<div style="display: flex">
    <div style="flex-wrap: nowrap">
        <?php
        include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/header.php");
        ?>
    </div>
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
            </tr>
            <?php
            $query = 'SELECT * FROM Competitions_infoV';
            $stmt = $conn->stmt_init();
            if ($stmt->prepare($query)) {
                $stmt->execute();
                $stmt->bind_result($id, $team, $player, $opponent, $amount);

                while ($stmt->fetch())
                    echo "<tr><td>$team</td><td>$player</td><td>$opponent</td><td>$amount</td><td class='edit-btn'><form method='POST' action='/IndZ/editors/players.php'><button type='submit' name='edit' value=$id.' '.$team.' '.$player>Изменить</button></form></td></tr>";
                $stmt->free_result();
                $stmt->close();
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
