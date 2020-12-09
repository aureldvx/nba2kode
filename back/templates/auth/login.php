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
<h1>Connexion utilisateur</h1>
<form action="<?= str_replace('/index.php', '', $_SERVER['PHP_SELF']) ?>" method="post">
  <div class="form-field">
    <label for="username">Identifiant</label>
    <input type="text" name="username" id="username">
  </div>
  <div class="form-field">
    <label for="password">Mot de passe</label>
    <input type="password" name="password" id="password">
  </div>
  <input type="hidden" name="_csrf_token" value="<?= $vars['_csrf_token'] ?>">
  <div class="formActions">
    <a href="/forgotten-password">Mot de passe oublié</a>
    <button type="submit">Se connecter</button>
  </div>
</form>
</body>
</html>