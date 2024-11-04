<?php
require  'functions.php';
if($_SERVER["REQUEST_METHOD"] == "POST"){
    changeStatus($_POST['id'],$_POST['online_status']);
    setFlashMessage('Статус успешно изменен!');
    redirect('index.php');
}