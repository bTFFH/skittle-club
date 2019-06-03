<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel='stylesheet' href='/IndZ/styles/table.css'>
    <link rel='stylesheet' href='/IndZ/styles/buttons.css'>
    <title>Команды клуба</title>
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
        <table cellpadding="5">
            <caption>Команды клуба</caption>
            <tr>
                <th>Команда</th>
                <th>Капитан</th>
                <th></th>
                <th></th>
            </tr>
            <?php
            $query = 'SELECT * FROM TeamsV';
            $stmt = $conn->stmt_init();
            if ($stmt->prepare($query)) {
                if ($stmt->execute()) {
                    $stmt->bind_result($id, $team, $captain);

                    while ($stmt->fetch())
                        echo "<tr><td>$team</td><td>$captain</td><td class='edit-btn'><form method='POST' action='/IndZ/actions/team.php'><button type='submit' name='edit' value=$id><img src='/IndZ/images/settings.svg' alt='Изменить'/></button></form></td><td class='edit-btn'><form method='POST' action='/IndZ/helpers/delete.php'><button type='submit' name='delete' value=\"" . $id . ' teams"' . "><img src='/IndZ/images/delete.svg' alt='Удалить'/></button></form></td></tr>";
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
