<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel='stylesheet' href='/IndZ/styles/table.css'>
    <link rel='stylesheet' href='/IndZ/styles/form.css'>
    <link rel='stylesheet' href='/IndZ/styles/buttons.css'>
    <title>Отчет об использовании площадок</title>
    <?php
    session_start();
    if (!isset($_SESSION['username']))
        header("Location: " . $_SERVER['DOCUMENT_ROOT'] . "/IndZ/");
    include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnOpen.php');
    ?>
</head>
<body>
<div style="display: flex">
    <div style="flex-wrap: nowrap">
        <?php
        include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/header.php');
        ?>
    </div>
    <?php
    if ($_SERVER['REQUEST_METHOD'] != "POST") {
        ?>
        <div class="general-form">
            <br/>
            <br/>
            <form name="PlaygroundsUsage" method="POST" action="playgrounds_usage.php">
                <p><label class="date-label">Дата начала:<input class="date-input" type="date" name="startDate"
                                                                value="<?php echo date("Y-m-d"); ?>" /></label></p>
                <p><label class="date-label">Дата конца:<input
                                class="date-input" type="date" name="endDate"
                                value="<?php echo date("Y-m-d", time() + 60 * 60 * 24 * 7); ?>" /></label></p>
                <div class="submit-btn"><input type="submit" value="Сформировать"/></div>
            </form>
        </div>
        <?php
    } else {
        $std = $_POST['startDate'] != "" ? $_POST['startDate'] : date("Y-m-d");
        $end = $_POST['endDate'] != "" ? $_POST['endDate'] : date("Y-m-d");
        $query = "CALL playgrounds_usage(?, ?)";
        $stmt = $conn->stmt_init();
        if ($stmt->prepare($query)) {
            $stmt->bind_param('ss', $std, $end);
            $stmt->execute();
            $result = $stmt->get_result();
        }
        $std = substr($std, 8, 2) . '.' . substr($std, 5, 2) . '.' . substr($std, 0, 4);
        $end = substr($end, 8, 2) . '.' . substr($end, 5, 2) . '.' . substr($end, 0, 4);
        ?>
        <div class="general-table">
            <br />
            <br />
            <br />
            <form name="repeatInput" method="GET" action="teams_participation.php">
                <div class="submit-btn-rev"><input type="submit" value="Повторить ввод"/></div>
            </form>
            <br/>
            <table cellpadding="3 5">
                <caption>Площадки в использовании с <?php echo $std; ?> по <?php echo $end; ?></caption>
                <tr>
                    <th>Площадка</th>
                    <th>Игравшиые команды</th>
                    <th>Дата игры</th>
                </tr>
                <?php
                while ($row = $result->fetch_row())
                    echo "<tr><td>$row[0]</td><td>$row[1]</td><td>$row[2]</td></tr>";

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
