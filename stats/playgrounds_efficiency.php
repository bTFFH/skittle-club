<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel='stylesheet' href='/IndZ/styles/table.css'>
    <title>Результативность по площадкам</title>
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
    <div class="general-table">
        <br />
        <br />
        <table cellpadding="3 5">
            <caption>Результативность по площадкам</caption>
            <tr>
                <th>Площадка</th>
                <th>Эффективность</th>
            </tr>
            <?php
            $query = 'CALL playgrounds_efficiency()';
            $stmt = $conn->stmt_init();

            if ($stmt->prepare($query)) {
                if ($stmt->execute()) {
                    $stmt->bind_result($playground, $efficiency);
                    $stmt->store_result();

                    while ($stmt->fetch()) {
                        if ($efficiency === null) continue;
                        echo "<tr><td>$playground</td><td>$efficiency</td></tr>";
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
include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnClose.php');
?>
</body>
</html>
