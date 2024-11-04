<?php
require 'functions.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (strtolower($_POST['password']) !== strtolower($_POST['confirm_password'])) {
        setFlashMessage('Неправильный пароль!','error');
        redirect('security.php');
    }
    if (empty($_POST['password']) || empty($_POST['email'])) {
        setFlashMessage('Поля не должны быть пустыми !','error');
        redirect('security.php');
    }

    if (!editCredentials($_POST['id'], $_POST['email'], $_POST['password'])) {
        setFlashMessage('Email занят','error');
        redirect('security.php');
    }

    redirect('index.php');

}