<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel="icon" href="/IndZ/images/logo.png">
    <link rel='stylesheet' href='/IndZ/styles/auth.css'>
    <title>Авторизация</title>
</head>
<body>
<div class="auth-form">
    <?php
    if ($_SERVER['REQUEST_METHOD'] != "POST") {
        ?>
        <form name="auth" method="POST" action="index.php">
            <p><label>Логин<input type="text" name="username" placeholder="username"/></label></p>
            <p><label>Пароль<input type="password" name="passwd" placeholder="password"/></label></p>
            <div class="submit-btn">
                <div><input type="button" value="Регистрация" onclick="reg()"/></div>
                <div><input type="submit" value="Вход"></div>
            </div>
        </form>
        <?php
        if (isset($_GET['err'])) {
            echo "<p><output style='padding-left: 15px;'>Неверное имя пользователя или пароль</output></p>";
            if ($_GET['err'] == 'p') echo "passtruoubles";
        }
    } else {
        require_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/treatment.php");
        include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/dbConnOpen.php");
        $query = 'SELECT username, passwd, name, salt FROM users WHERE username = ?';
        $stmt = $conn->stmt_init();
        if ($stmt->prepare($query)
            && $stmt->bind_param('s', treat($_POST['username']))
            && $stmt->execute()
        ) {
            $stmt->bind_result($username, $pass, $name, $salt);
            $stmt->store_result();
            if ($stmt->num_rows === 0) {
                $stmt->free_result();
                $stmt->close();
                include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/dbConnClose.php");
                echo "<input name='err' value='t' hidden />";
                header("Location: index.php?err=t", true);
            } else {
                $stmt->fetch();
                $pwd = cryptCheckPass($_POST['passwd'], $salt);
                if ($pwd == $pass) {
                    session_start();
                    $_SESSION['username'] = $username;
                    $_SESSION['name'] = $name;
                    $stmt->free_result();
                    $stmt->close();
                    include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/dbConnClose.php");
                    header("Location: welcome.php", true);
                } else {
                    $stmt->free_result();
                    $stmt->close();
                    include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/dbConnClose.php");
                    header("Location: index.php?err=p", true);
                }
            }
        } else {
            include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/dbConnClose.php");
            header("Location: index.php?err=t", true);
        }

        session_start();
    }
    ?>
</div>
<script>
    function reg() {
        location.replace("registration.php");
    }
</script>
</body>
</html>
