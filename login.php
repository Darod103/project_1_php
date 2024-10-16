<?php
require('functions.php');

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = $_POST["email"];
    $password = $_POST["password"];
    if(login($email, $password)){
        redirect('users.php');
    }
    setFlashMessage('Не верный логин или пороль.','danger');
    redirect('page_login.php');

}