<!DOCTYPE html>
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/statsHead.php'); ?>
    <link rel='stylesheet' href='/IndZ/styles/form.css'>
    <link rel='stylesheet' href='/IndZ/styles/buttons.css'>
    <title>Отчет об участии команд</title>
</head>
<body>
<div style="display: flex">
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/header.php');
    ?>
    <?php
    if ($_SERVER['REQUEST_METHOD'] != "POST") {
        ?>
        <div class="general-form">
            <br/>
            <br/>
            <form name="teamsParticipation" method="POST" action="teams_participation.php">
                <div class="date">
                    <p><label style="color: navy">Отчет об участии команд</label></p>
                    <p><label>Дата начала:<input type="date" name="startDate"
                                                 value="<?php echo date("Y-m-d"); ?>" /></label></p>
                    <p><label>Дата конца:<input type="date" name="endDate"
                                                value="<?php echo date("Y-m-d", time() + 60 * 60 * 24 * 7); ?>" /></label></p>
                </div>
                <div class="submit-btn" style="padding-left: 202px"><input type="submit" value="Сформировать"/></div>
            </form>
        </div>
        <?php
    } else {
        $std = $_POST['startDate'] != "" ? $_POST['startDate'] : date("Y-m-d");
        $end = $_POST['endDate'] != "" ? $_POST['endDate'] : date("Y-m-d");
        $query = "CALL teams_participation(?, ?)";
        $stmt = $conn->stmt_init();
        if ($stmt->prepare($query)) {
            $stmt->bind_param('ss', $std, $end);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
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
        $std = substr($std, 8, 2) . '.' . substr($std, 5, 2) . '.' . substr($std, 0, 4);
        $end = substr($end, 8, 2) . '.' . substr($end, 5, 2) . '.' . substr($end, 0, 4);
        ?>
        <div class="general-table">
            <br/>
            <br/>
            <br/>
            <form name="repeatInput" method="GET" action="teams_participation.php">
                <div class="submit-btn-rev"><input type="submit" value="Повторить ввод"/></div>
            </form>
            <br/>
            <table cellpadding="3 5">
                <caption>Команды участники с <?php echo $std; ?> по <?php echo $end; ?></caption>
                <tr>
                    <th>Команда</th>
                    <th>Капитан</th>
                    <th>Состав</th>
                    <th>Дата</th>
                </tr>
                <?php


                while ($row = $result->fetch_row())
                    echo "<tr><td>$row[0]</td><td>$row[1]</td><td>$row[2]</td><td>$row[3]</td></tr>";

                $stmt->free_result();
                $stmt->close();

                ?>
            </table>
        </div>
        <?php
    }
    ?>
</div>
<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnClose.php');
?>
</body>
</html>
