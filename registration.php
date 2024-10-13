<?php
session_start();
require('functions.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    if (getUserByEmail($email)){
        setFlashMessage('Этот эл. адрес уже занят другим пользователем.','danger');
        redirect("page_register.php");
    }
    addUser($email, $password);
    setFlashMessage('Регистрация успешна');
    redirect("page_login.php");
}


