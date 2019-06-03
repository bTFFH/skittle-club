<!DOCTYPE html>
<html>
<head>
    <meta charset='utf8'>
    <link rel='stylesheet' href='/IndZ/styles/auth.css'>
    <title>Регистрация</title>
</head>
<body>
<div class="auth-form">
    <?php
    $pattern = ".*";
    //    $pattern = "/(([0-9]+[A-Z]+[a-z]+)|([A-Z]+[a-z]+[0-9]+)|([a-z]+[A-Z]+[0-9]+)|([A-Z]+[0-9]+[a-z]+)|([0-9]+[a-z]+[A-Z]+)|([a-z]+[0-9]+[A-Z]+))/";
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        require_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/treatment.php");
        $username = treat($_POST['username']);
        $passwd = treat($_POST['passwd']);
        echo $username;
        if ($username == false) {
            header("Location: registration.php/?err=l", true);
        } elseif ($passwd === false) {
            header("Location: registration.php/?err=p", true);
        } else {
            include_once($_SERVER['DOCUMENT_ROOT'] . '/IndZ/helpers/dbConnOpen.php');
            $query = 'SELECT username FROM users WHERE username = ?';
            $stmt = $conn->stmt_init();
            if ($stmt->prepare($query)) {
                $stmt->bind_param('s', $username);
                if ($stmt->execute()) {
                    $stmt->store_result();
                    if ($stmt->num_rows !== 0) {
                        $stmt->free_result();
                        $stmt->close();
                        include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/dbConnClose.php");
                        header("Location: registration.php/?err=l", true);
                    } else {
                        $stmt->free_result();
                        $query = "INSERT INTO `users`(`username`, `name`, `surname`, `passwd`) VALUES (?, ?, ?, ?)";
                        if ($stmt->prepare($query)) {
                            $passwd = md5($passwd);
                            $stmt->bind_param('ssss', $_POST['username'], $_POST['name'], $_POST['surname'], $passwd);
                            if ($stmt->execute()) {
                                $stmt->close();
                                include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/dbConnClose.php");
                                header("Location: index.php");
                            } else {
                                $stmt->close();
                                include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/dbConnClose.php");
                                header("Location: registration.php/?err=t", true);
                            }
                        } else {
                            $_SESSION['errno'] = $stmt->errno;
                            $_SESSION['error'] = $stmt->error;
                            header("Location: ../helpers/error.php");
                        }
                    }
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
            include_once($_SERVER['DOCUMENT_ROOT'] . "/IndZ/helpers/dbConnClose.php");
        }
    } else {
        ?>
        <form name="auth" method="POST" action="registration.php">
            <p><label>Имя<input type="text" name="name" maxlength="50" pattern="[A-ZА-Я^ЪЬ]{1}[A-ZА-ЯЁa-zа-яё -]{1,49}"
                                placeholder="Adam" required/></label></p>
            <p><label>Фамилия<input type="text" name="surname" maxlength="100"
                                    pattern="[A-ZА-Я^ЪЬ]{1}[A-ZА-ЯЁa-zа-яё -]{1,99}" placeholder="Smith"
                                    required/></label></p>
            <p><label>Логин<input type="text" name="username" minlength="2" maxlength="10" placeholder="MyLogin"
                                  required/></label></p>
            <p data-tooltip="Пароль должен содержать заглавные и строчные символы, цифры и иметь длниу не менее 6 символов">
                <label>Пароль<input type="password" name="passwd" minlength="6"
                                    pattern=<?php echo $pattern; ?> placeholder="password123" required/></label></p>
            <div class="submit-btn"><input type="submit" value="Регистрация" style="margin-left: 120px"/></div>
        </form>
        <?php
        if (isset($_GET['err']) && $_GET['err'] == 'l') {
            echo "<p><output>Данное имя пользователя уже существует</output></p>";
        }
        if (isset($_GET['err']) && $_GET['err'] == 'p') {
            echo "<p><output style='margin-left: 60px;'>Данный пароль не валиден</output></p>";
        }
        if (isset($_GET['err']) && $_GET['err'] == 't') {
            echo "<p><output>При создании пользователся возникла ошибка.</output></p>";
            echo "<p><output>Пожалуйста, зарегистрируйтесь заново</output></p>";
        }
    }
    ?>

</div>
</body>
</html>