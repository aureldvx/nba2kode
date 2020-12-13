<?php
if (!defined('__APP_SECRET__')) {
    header('Location: /');
    die();
}

if (isset($_SESSION['flashes']) && count($_SESSION['flashes']) > 0) : ?>
    <aside class="flex flex-col items-stretch fixed w-full mt-20">
    <?php foreach ($_SESSION['flashes'] as $flash) : ?>
    <div class="w-11/12 ml-auto mr-auto p-4 bg-blue-50 text-blue-500">
        <strong class="capitalize"><?= $flash['type'] ?></strong>
        <p><?= $flash['message'] ?></p>
    </div>
    <?php endforeach; ?>
    </aside>
<?php $_SESSION['flashes'] = [] ?>
<?php endif; ?>