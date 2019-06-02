<!DOCTYPE html>
<html>
<head>
    <meta charset="utf8">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lora&display=swap&subset=cyrillic">
    <?php
    session_start();
    if (!isset($_SESSION['username']))
        header("Location: " . $_SERVER['DOCUMENT_ROOT'] . "/IndZ/");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/dbConnOpen.php");
    ?>
</head>
<body>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->stmt_init();
    $url = explode(' ', $_POST['delete']);
    $query = 'DELETE FROM ' . $url[1] . ' WHERE id = ?';
    if ($stmt->prepare($query)) {
        $stmt->bind_param('i', $url[0]);
        if ($stmt->execute()) $stmt->close();
    } else {
        $_SESSION['errno'] = $stmt->errno;
        $_SESSION['error'] = $stmt->error;
        echo "error in preparing";
//        header("Location: error.php");
    }
    ?>
    <div style="display: flex; flex-wrap: nowrap; flex-direction: row;">
        <?php
        include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/header.php');
        ?>
        <div style="display: flex; flex-wrap: wrap; flex-direction: column; margin: 0 auto">
            <div style="padding-top: 200px">
                    <p>
                        <output style="font-family: 'Lora', serif; font-size: 22px; font-style: normal; color: seagreen">Запись успешно удалена</output>
                    </p>
            </div>
        </div>
    </div>
    <?php
} else {
    header("Location: " . $_SERVER['DOCUMENT_ROOT'] . "/IndZ/");
}
?>
</body>
</html>
