<?php
function treat($str) {
    $str = stripcslashes($str);  // удаляет экранированные символы
    $str = stripslashes($str);  // удаляет все слеши, если остались (на деле - параноя)
    $str = strip_tags($str);  // удаляет все HTML и PHP теги
    $str = htmlspecialchars($str);  // переводит спецсимволы в их "код", например '&' станет '$amp'
    $str = trim($str);  // удаляет граничащие пробелы
    // паттерны взяты из великой и могучей сети интернет
    $pattern = "/.*((root)|(bin)|(daemon)|(adm)|(lp)|(sync)|(shutdown)|(halt)|
    (mail)|(news)|(uucp)|(operator)|(games)|(mysql)|(httpd)|(nobody)|(dummy)|
    (www)|(cvs)|(shell)|(ftp)|(irc)|(debian)|(ns)|(download)|(false)).*/i";
    if ( preg_match($pattern, $str) or preg_match("/^(anoncvs_)/", $str) )
        return false;
    return $str;
}
?>

