<?php
session_start();
if ( !isset($_SESSION['username']) )
    header("Location: " . $_SERVER['DOCUMENT_ROOT'] . "/IndZ/");
