<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel='stylesheet' href='/IndZ/styles/addition.css'>
    <link rel='stylesheet' href='/IndZ/styles/buttons.css'>
    <title>Добавление площадки</title>
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
    <div class="add-form" style="flex-basis: 500px">
        <br/>
        <br/>
        <?php
        $stmt = $conn->stmt_init();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['edit'])) {
            if ($_SESSION['update'] === "Not updated") {
                $query = 'INSERT INTO `playgrounds`(`name`, `features`) VALUES (?, ?)';
                if ($stmt->prepare($query)) {
                    $stmt->bind_param('ss', $_POST['name'], $_POST['features']);
                    if ($stmt->execute()) {
                        echo "<p><output style='color: seagreen;'>Новая площадка успешно добавлена</output></p>";
                    } else {
                        ?>
                        <form name="errorNewPlayground" method="GET" action="playground.php">
                            <p><output>При добавлении площадки возникла ошибка</output></p>
                            <p><output>Попробойте осуществить операцию еще раз</output></p>
                            <div class="submit-btn"><input type="submit" value="Попробовать"/></div>
                        </form>
                        <?php
                    }
                }
            } else {
                $query = 'UPDATE `playgrounds` SET `name` = ?, `features` = ? WHERE `id` = ?';
                if ($stmt->prepare($query)) {
                    $stmt->bind_param('ssi', $_POST['name'], $_POST['features'], $_SESSION['update']);
                    if ($stmt->execute()) {
                        unset($_SESSION['update']);
                        echo "<p><output style='color: seagreen;'>Площадка была успешно обновлена</output></p>";
                    } else {
                        ?>
                        <form name="errorUpdPlayground" method="POST" action="playground.php">
                            <p><output>При обновлении площадки возникла ошибка</output></p>
                            <p><output>Попробойте осуществить операцию еще раз</output></p>
                            <div class="submit-btn"><input type="submit" value="Попробовать"/></div>
                            <input type="text" name="name" value="<?php echo $_POST['name']; ?>" hidden />
                            <textarea name="features" hidden><?php echo $_POST['features']; ?></textarea>
                            <input type="text" name="edit" value="<?php echo $_SESSION['update']; ?>" hidden />
                        </form>
                        <?php
                    }
                }
            }
        $stmt->close();
        } else {
            $_SESSION['update'] = "Not updated";
            $name = '';
            $features = '';
            if (isset($_POST['edit'])) {
                $_SESSION['update'] = $_POST['edit'];
                $query = 'SELECT name, features FROM playgrounds WHERE id = ?';
                if ($stmt->prepare($query)) {
                    $stmt->bind_param('i', $_POST['edit']);
                    if ($stmt->execute()) {
                        $stmt->bind_result($name, $features);
                        $stmt->store_result();
                        $stmt->fetch();
                        $stmt->free_result();
                        $stmt->close();
                        if ($features === "Нет данных") $features = '';
                    }
                }
            }
            ?>
            <form name="insertNewPlayground" method="POST" action="playground.php">
                <p><label>Название площадки<input type="text" name="name" maxlength="50"
                                                  pattern="[0-9A-ZА-Я^ЪЬ]{1}.*" value="<?php echo $name; ?>"
                                                  placeholder="Underground"/></label></p>
                <p><label>Особенности<textarea name="features" maxlength="255" wrap="soft"
                                               placeholder="Ambience of the UK underground"><?php echo $features; ?></textarea></label></p>
                <div class="submit-btn" style="padding: 40px 0 0 0;"><input
                            type="submit"
                            value="<?php if (!isset($_POST['edit'])) echo 'Добавить'; else echo 'Обновить'; ?>"/></div>
            </form>
            <?php
        }
        ?>
    </div>

</div>
<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnClose.php');
?>
</body>
</html>
