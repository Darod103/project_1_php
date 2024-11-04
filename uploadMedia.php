<?php
require 'functions.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    uploadAvatar($_POST['user_id'],'./upload/',$_FILES);
    redirect('index.php');
}