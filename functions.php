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

//Получаем флеш сообщение елси тип такой есть то возврашем , по умолчанию success для удобства вывода
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

// Функция добовляет прова админа пользывателю по email
function addAdminRole ($email){
    $pdo = db('127.0.0.1','test','root','');
    $stmt = $pdo->prepare("update users set is_admin = 1 where email = :email");
    $stmt->execute(['email' => $email]);
    if($stmt->rowCount() > 0){
        return true;
    }
    else return false;
}

// Проверка ялвяеться ли пользыватель админом
function isAdmin($email)
{
    $pdo = db('127.0.0.1','test','root','');
    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return !($result['is_admin'] === 0);
}

// Функции логина которая проверяет есть ли такой пользыватель в бд и сравниваеть пороли
function login($email, $password){
    $user = getUserByEmail($email);
    if($user && password_verify($password, $user['password'])){
        $_SESSION['email'] = $user['email'];
        $_SESSION['is_admin'] = isAdmin($email);
        $_SESSION['id'] = $user['id'];
        return true;
    }
    return false;
}

// Выводи всех пользывателей из 2 таблиц
function getAllUsers()
{   $pdo = db('127.0.0.1','test','root','');
    $stmt = $pdo->prepare("SELECT u.email, u.id,d.* FROM users u left join users_details d on u.id = d.user_id ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//var_dump(getAllUsers());