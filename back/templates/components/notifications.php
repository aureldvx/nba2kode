<?php
if (!defined('__APP_SECRET__')) {
    header('Location: /');
    die();
}

if (isset($_SESSION['flashes']) && count($_SESSION['flashes']) > 0) : ?>
    <aside class="notifications">
    <?php foreach ($_SESSION['flashes'] as $flash) : ?>
    <div class="notification <?= $flash['type'] ?>">
        <strong><?= $flash['type'] ?></strong>
        <p><?= $flash['message'] ?></p>
    </div>
    <?php endforeach; ?>
    </aside>
<?php $_SESSION['flashes'] = [] ?>
<?php endif; ?>