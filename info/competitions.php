<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel='stylesheet' href='/IndZ/styles/table.css'>
    <link rel='stylesheet' href='/IndZ/styles/buttons.css'>
    <title>История игр</title>
    <?php
    session_start();
    if (!isset($_SESSION['username']))
        header("Location: " . $_SERVER['DOCUMENT_ROOT'] . "/IndZ/");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/dbConnOpen.php");
    $conn->set_charset('utf8');
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
            if ($stmt->prepare($query)) {
                if ($stmt->execute()) {
                    $stmt->bind_result($id, $team1, $team2, $playground, $gameDate, $absence);

                    while ($stmt->fetch()) {
                        $gameDate = substr($gameDate, 8, 2) . '/' . substr($gameDate, 5, 2) . '/' . substr($gameDate, 0, 4);
                        echo "<tr><td>$team1</td><td>$team2</td><td>$playground</td><td>$gameDate</td><td>$absence</td><td class='edit-btn'><form method='POST' action='/IndZ/actions/game.php'><button type='submit' name='edit' value=$id><img src='/IndZ/images/settings.svg' alt='Изменить'/></button></form></td><td class='edit-btn'><form method='POST' action='/IndZ/helpers/delete.php'><button type='submit' name='delete' value=\"" . $id . ' competitions"' . "><img src='/IndZ/images/delete.svg' alt='Удалить'/></button></form></td></tr>";
                    }
                    $stmt->free_result();
                    $stmt->close();
                } else {
                    $_SESSION['errno'] = $stmt->errno;
                    $_SESSION['error'] = $stmt->error;
                    header("Location: ../helpers/error.php");
                }
            } else {
                $_SESSION['errno'] = $stmt->errno;
                $_SESSION['error'] = $stmt->error;
                header("Location: ../helpers/error.php");
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
