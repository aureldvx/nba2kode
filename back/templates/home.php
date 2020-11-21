<?php
if (!defined('__APP_SECRET__')) {
    header('Location: /');
    die();
}

echo 'this is home';
echo $vars['title'];
