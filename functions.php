<?php
require_once 'db.php';
session_start();
$pdo = db();

//Получаем пользователь по email добавлена проверка на подключение к базе
function getUserByEmail($email)
{
    global $pdo;

    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            setFlashMessage('Query Failed!' . $e->getMessage(), 'QueryError');
        }
    }
    return false;
}

//Выводим детали пользователя по id
function getUserDetails($userId)
{
    global $pdo;
    if ($pdo) {
        $stmt = $pdo->prepare("SELECT * FROM users_details WHERE user_id = :id");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
}

;
// Вывод с 2 бд данных  пользователя по id
function getUserById($userId)
{
    global $pdo;
    if ($pdo) {
        $stmt = $pdo->prepare("SELECT * FROM users 
                                     left join users_details  
                                    on users.id = users_details.user_id
                                    where id = :userId");
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
}

//Добавляем пользователя и возвращаем userId
function addUser($email, $password): int
{
    global $pdo;

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
    $stmt->execute(['email' => $email, 'password' => $hashedPassword]);
    return (int)$pdo->lastInsertId();


}

// Проверка статуса пользователя
function checkStatus($status): string
{
    $result = '';
    switch ($status) {
        case 'online':
            $result = 'success';
            break;
        case 'away':
            $result = 'warning';
            break;
        case 'do_not_disturb':
            $result = 'md';
            break;
    }
    return $result;

}

//Функция меняет статус пользователя.
function changeStatus($userId, $online_status): bool
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users_details SET online_status = :online_status WHERE user_id = :userId");
    $stmt->execute(['online_status' => checkStatus($online_status), 'userId' => $userId]);
    if ($stmt->rowCount() > 0) {
        return true;
    }
    return false;
}

//Добавляем детали юзеру по id
function addUserDetails($userId, $name, $workplace, $phone, $address, $status = 'md', $vk_link = null, $telegram_link = null, $instagram_link = null)
{
    global $pdo;
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

// Передаем массив и изменяем у юзера имя,место работы, телефон и адрес
function editUserDetails($user)
{
    global $pdo;
    $stmt = $pdo->prepare('update users_details set name=:name, workplace=:workplace,phone=:phone,address=:address where user_id=:user_id');
    $stmt->execute(['name' => $user['name'],
        'workplace' => $user['workplace'],
        'phone' => $user['phone'],
        'address' => $user['address'],
        'user_id' => $user['user_id']]);


}

//Создаем флеш-сообщение, по умолчанию стоит success
function setFlashMessage($message, $type = 'success')
{
    return $_SESSION['flash_message'][$type] = $message;
}

//Получаем флеш-сообщение, если тип такой есть то возвращаем . По умолчанию success для удобства вывода.
function getFlashMessage($type = 'success')
{
    if (isset($_SESSION['flash_message'][$type])) {
        $message = $_SESSION['flash_message'][$type];
        unset($_SESSION['flash_message'][$type]);
        return $message;
    }
    return null;
}

// Редирект на определенную страницу
function redirect($url): void
{
    header('Location: ' . $url);
    exit();
}

// Функция добавляет права админа пользователю по email
function addAdminRole($email): bool
{
    global $pdo;
    $stmt = $pdo->prepare("update users set is_admin = 1 where email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->rowCount() > 0) {
        return true;
    } else return false;
}

// Проверка являеться ли пользователь админом
function isAdmin($email): bool
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return !($result['is_admin'] === 0);
}

// Функция логина, которая проверяет, есть ли такой пользователь в БД и сравнивает пароли.
// При успешной проверке передает в сессию email, id и информацию о том, является ли пользователь администратором.
function checkUser($email, $password): bool
{
    $user = getUserByEmail($email);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['email'] = $user['email'];
        $_SESSION['is_admin'] = isAdmin($email);
        $_SESSION['id'] = $user['id'];
        return true;
    }
    return false;
}

//Функция меняет пароль и email пользователя
function editCredentials($userId, $email, $password): bool
{
    $user = getUserByEmail(strtolower(strtolower($email)));
    if ($user && $user['id'] != $userId) {
        return false;
    }
    global $pdo;
    $stmt = $pdo->prepare('update users set email=:email, password=:password where id = :userId');
    $stmt->execute(['email' => strtolower($email), 'password' => password_hash($password, PASSWORD_DEFAULT), 'userId' => $userId]);
    return true;


}

// Вывод всех пользывателей из двух таблиц
function getAllUsers()
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT u.email, u.id,d.* FROM users u left join users_details d on u.id = d.user_id ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//Загрузка аватарки в бд и в указанную дерикторию
function uploadAvatar($userId, $uploadDir, $file): bool
{
    // Проверяем есть ли такая папка , если нет то создаем ее !
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $newFileName = uniqid() . '.' . pathinfo($file["file"]['name'], PATHINFO_EXTENSION);
    $uploadPath = rtrim($uploadDir, '/') . '/' . $newFileName;

    //Проверяем  формат передаваемых файлов если формата нет в массиве возвращаем false
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['file']['type'], $allowedTypes)) {
        return false;
    }

    if (move_uploaded_file($file['file']['tmp_name'], $uploadPath)) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE users_details SET avatar = :avatar WHERE user_id = :id");
        $stmt->execute(['avatar' => $uploadPath, 'id' => $userId]);
        return true;
    } else {
        return false;
    }
}

;

//Функция удаления пользователя из бд по id
function deleteUser($userId): bool
{
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :userId");
    $stmt->execute(['userId' => $userId]);
    if ($stmt->rowCount() > 0) {
        return true;
    }
    return false;
}