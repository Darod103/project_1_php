<?php
require 'functions.php';
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $user = [
        'user_id'=> $_POST['id'],
        "name" => $_POST['name'],
        'workplace'=>$_POST['workplace'],
        'phone'=>$_POST['phone'],
        'address'=>$_POST['address'],
    ];
    // Проверяем есть ли такой юзер в users_details
    if(!getUserDetails($user['user_id'])){
        addUserDetails($user['user_id'],$user['name'],$user['workplace'],$user['phone'],$user['address']);
        setFlashMessage("Данные {$user['name']} успешно изменены");
        redirect("index.php");
    }


    editUserDetails($user);
    setFlashMessage("Данные {$user['name']} успешно изменены");
    redirect("index.php");
}