<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel='stylesheet' href='/IndZ/styles/table.css'>
    <title>Игральные площадки</title>
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
        <br/>
        <br/>
        <table cellpadding="3 5">
            <caption>Игральные площадки</caption>
            <tr>
                <th>Название</th>
                <th style='width: 75%'>Описание, особенности</th>
                <th></th>
            </tr>
            <?php
            $query = 'SELECT * FROM PlaygroundsV';
            $stmt = $conn->stmt_init();
            if ($stmt->prepare($query)) {
                $stmt->execute();
                $stmt->bind_result($id, $name, $features);

                while ($stmt->fetch()) {
                    if ($features == '') $features = 'Нет данных';
                    echo "<tr><td>$name</td><td >$features</td><td class='edit-btn'><form method='POST' action='/IndZ/actions/playground.php'><button type='submit' name='edit' value=$id><img src='/IndZ/images/settings.svg' alt='Изменить'/></button></form></td></tr>";
                }

                $stmt->free_result();
                $stmt->close();
            }
            ?>
        </table>
    </div>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/dbConnClose.php");
    ?>
</body>
</html>
