<?php
require 'functions.php';
$user = getUserById($_GET['id']);
if ($user['is_admin']) {
   setFlashMessage('Админа нельзя удалить !','error');
   redirect('index.php');
}
deleteUser($user['id']);
setFlashMessage('Пользователь успешно удалён.');
redirect('index.php');