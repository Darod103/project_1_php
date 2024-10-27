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
    return false;
}
function getUserDetails($userId)
{   $pdo = db('127.0.0.1','test','root','');
    if($pdo) {
        $stmt = $pdo->prepare("SELECT * FROM users_details WHERE user_id = :id");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
};
//Добовляем пользывателя и возврощает userId
function addUser($email, $password)
{
  $pdo = db('127.0.0.1','test','root','');

  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
  $stmt->execute(['email' => $email, 'password' => $hashedPassword]);
  return (int)$pdo->lastInsertId();


}
// Проверка статуса пользывателя
function checkStatus($status)
{
    $result ='';
    switch ($status) {
        case 'online':
            $result = 'success';
            break;
        case 'away':
            $result = 'md';
            break;
        case 'do_not_disturb':
            $result = 'warning';
            break;
    }
    return $result;

}

//Добовляеми детали юзеру по айди
function addUserDetails($userId, $name, $workplace,$phone,$address,$status = 'md',$vk_link = null,$telegram_link = null ,$instagram_link= null){
    $pdo = db('127.0.0.1','test','root','');
    $stmt = $pdo->prepare('insert into users_details (user_id,name,workplace,phone,address,online_status,vk_link,telegram_link,instagram_link) value (:user_id,:name,:workplace,:phone,:address,:online_status,:vk_link,:telegram_link,:instagram_link) ');
    $stmt->execute([
        'user_id' => $userId,
        'name' => $name,
        'workplace' => $workplace,
        'phone' => $phone,
        'address' => $address,
        'online_status' => $status,
        'vk_link' => $vk_link,
        'telegram_link' => $telegram_link,
        'instagram_link' => $instagram_link
    ]);
}

// Передаем массив и изменяем у юзера имя,место работы, телефон и адреес
function editUserDetails($user)
{
    $pdo = db('127.0.0.1','test','root','');
    $stmt = $pdo->prepare('update users_details set name=:name, workplace=:workplace,phone=:phone,address=:address where user_id=:user_id');
    $stmt->execute(['name'=>$user['name'],
        'workplace'=>$user['workplace'],
        'phone'=>$user['phone'],
        'address'=>$user['address'],
        'user_id'=>$user['user_id']]);


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
function login($email, $password): bool
{
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

function uploadAvatar($userId,$uploadDir,$file): bool
{
    // Проверяем есть ли такая папка , если нет то создаем ее !
    if(!is_dir($uploadDir)){
        mkdir($uploadDir, 0777, true);
    }

    $newFileName = uniqid().'.'.pathinfo($file["file"]['name'], PATHINFO_EXTENSION);
    $uploadPath = rtrim($uploadDir, '/') . '/' . $newFileName;

    //Проверяем  формат передоваемых файлов если формата нет в массиве возвращем false
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['file']['type'], $allowedTypes)) {
        return false;
    }

    if(move_uploaded_file($file['file']['tmp_name'], $uploadPath)){
        $pdo = db('127.0.0.1','test','root','');
        $stmt = $pdo->prepare("UPDATE users_details SET avatar = :avatar WHERE user_id = :id");
        $stmt->execute(['avatar' =>$uploadPath, 'id' => $userId]);
        return true;
    }else{
        return false;
    }
};
