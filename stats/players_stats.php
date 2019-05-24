<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel='stylesheet' href='/IndZ/styles/table.css'>
    <title>Статистика игроков</title>
    <?php
    session_start();
    if ( !isset($_SESSION['username']) )
        header("Location: " . $_SERVER['DOCUMENT_ROOT'] . "/IndZ/");
    include_once($_SERVER['DOCUMENT_ROOT']."/IndZ/helpers/dbConnOpen.php");
    $conn->set_charset('utf8');
    ?>
</head>
<body>
<div style="display: flex">
    <div style="flex-wrap: nowrap">
<?php
include_once($_SERVER['DOCUMENT_ROOT']."/IndZ/helpers/header.php");
?>
    </div>
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
        if ( $stmt->prepare( $query ) ) {
            $stmt->execute();
            $stmt->bind_result($id, $name, $surname, $team, $skAmount, $skWeekAmount, $gamesAmount, $effectiviness);

            while ( $stmt->fetch() )
                echo "<tr><td>$name $surname</td><td>$team</td><td>$skAmount</td><td>$skWeekAmount</td><td>$gamesAmount</td><td>$effectiviness</td></tr>";
            $stmt->free_result();
            $stmt->close();
        }
        ?>
    </table>
    </div>
</div>
<?php
include_once($_SERVER['DOCUMENT_ROOT']."/IndZ/helpers/dbConnClose.php");
?>
</body>
</html>
