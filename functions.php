<?php
session_start();

// Подключаемся к бд если есть ошибка то записывам ее в лог
function db($host, $db, $user, $pass)
{
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8";
    try {
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }catch (PDOException $e){
        error_log('Connection failed: ' . $e->getMessage());
        return null;
    }

}

//Получаем пользывателя по емейлу добавлина проверка на подключение к базе
function getUserByEmail($email) {
    $pdo = db('127.0.0.1','test','root','');
    if($pdo) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return null;
}

//Добовляем пользывателя
function addUser($email, $password)
{
  $pdo = db('127.0.0.1','test','root','');

  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
  $stmt->execute(['email' => $email, 'password' => $hashedPassword]);

}

//Создаем флеш сообщение по умолчанию стоит success
function setFlashMessage($message, $type = 'success')
{
    return $_SESSION['flash_message'][$type] = $message;
}

//Получаем флеш сообщение тип оп умолчанию success для удобства вывода
function getFlashMessage($type = 'success'){
    if(isset($_SESSION['flash_message'][$type])){
        $message = $_SESSION['flash_message'][$type];
        unset($_SESSION['flash_message'][$type]);
        return $message;
    }
    return null;
}

// Редирект на определенную страницу
function redirect($url){
    header('Location: '.$url);
    exit();
}


