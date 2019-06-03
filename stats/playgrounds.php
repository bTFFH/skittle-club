<!DOCTYPE html>
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/statsHead.php'); ?>
    <title>Статистика игр по площадкам</title>
</head>
<body>
<div style="display: flex">
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/header.php');
    ?>
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
                if ($stmt->execute()) {
                    $stmt->bind_result($id, $name, $features, $amount);

                    while ($stmt->fetch()) {
                        if ($features == '') $features = 'Нет данных';
                        echo "<tr><td>$name</td><td style='max-width: 550px'>$features</td><td>$amount</td></tr>";
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
include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnClose.php');
?>
</body>
</html>
