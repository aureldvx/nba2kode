<?php
if (!defined('__APP_SECRET__')) {
    header('Location: /');
    die();
}
?>
<!doctype html>
<html lang="fr">
<head>
    <?php include_once __TEMPLATES_DIR__ . 'layout/head.php'; ?>
    <title>Document</title>
</head>
<body class="page-login">
<?php include_once __TEMPLATES_DIR__ . 'components/notifications.php'?>
<h1>Création d'un compte utilisateur</h1>
<form action="<?= str_replace('/index.php', '', $_SERVER['PHP_SELF']) ?>" method="post">
    <div class="form-field">
        <label for="username">Nom d'utilisateur</label>
        <input type="text" name="username" id="username">
    </div>
    <div class="form-field">
        <label for="email">Email</label>
        <input type="email" name="email" id="email">
    </div>
    <div class="form-field">
        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password">
    </div>
    <div class="form-field">
        <label for="confirm_password">Confirmer le mot de passe</label>
        <input type="password" name="confirm_password" id="confirm_password">
    </div>
    <input type="hidden" name="_csrf_token" value="<?= $vars['_csrf_token'] ?>">
    <button type="submit">Créer mon compte</button>
</form>
</body>
</html>