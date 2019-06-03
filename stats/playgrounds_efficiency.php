<!DOCTYPE html>
<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/statsHead.php'); ?>
    <title>Результативность по площадкам</title>
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
