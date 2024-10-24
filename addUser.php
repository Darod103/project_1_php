<?php
require 'functions.php';
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = $_POST["email"];
    $password = $_POST["password"];
    if(empty($_POST['email']) && empty($_POST['password'])){
        setFlashMessage('Поля Email или пароль должны быть заполнены !','error');
        redirect('create_user.php');
    }
    if(getUserByEmail($email)){
        setFlashMessage('Такой email занят','error');
        redirect('create_user.php');
    }

    $userId= addUser($email, $password); // Добовляем пользывателя и возвращем его id

    $user = [
        'userId' => $userId,
        'name'=>$_POST["name"],
        'workplace'=>$_POST["workplace"],
        'phone'=>$_POST["phone"],
        'address'=>$_POST["address"],
        'online_status'=>checkStatus($_POST["online_status"]),
        'vk_link'=>$_POST["vk_link"],
        'telegram_link'=>$_POST["telegram_link"],
        'instagram_link'=>$_POST["instagram_link"],

    ];
    // Добовляем детали пользыв
    addUserDetails($user['userId'], $user['name'], $user['workplace'], $user['phone'], $user['address'], $user['online_status'], $user['vk_link'], $user['telegram_link'], $user['instagram_link']);
    //Добовляем аватар если не пустой
    if(!empty($_FILES['file']['tmp_name'])){
        if(!uploadAvatar($userId,'upload/',$_FILES)){
            setFlashMessage('Можно загрузить только картинку','error');
            redirect('create_user.php');
        }
    }
    setFlashMessage('Пользыватель успешно добавлен !');
    redirect('users.php');

}