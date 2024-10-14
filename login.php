<?php
session_start();
require('functions.php');

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = $_POST["email"];
    $password = $_POST["password"];
    if(login($email, $password)){
        setFlashMessage('Вы успешно вошли');
        redirect('users.php');
    }
    setFlashMessage('Не верный логин или пороль.','danger');
    redirect('page_login.php');

}