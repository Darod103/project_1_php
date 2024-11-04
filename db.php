<?php
// Подключаемся к бд если есть ошибка то записываем ее в лог
function db($host='127.0.0.1', $db='test', $user='root', $pass='')
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