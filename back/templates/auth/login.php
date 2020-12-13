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
<body class="h-screen w-full flex items-center justify-center font-sans">
<?php include_once __TEMPLATES_DIR__ . 'components/notifications.php'?>
<form action="<?= str_replace('/index.php', '', $_SERVER['PHP_SELF']) ?>" method="post" class="rounded-md shadow-lg flex flex-col items-stretch w-2/12 p-4">
  <h1 class="text-2xl font-bold text-center">Connexion utilisateur</h1>
  <div class="mt-5">
    <label for="username">Identifiant</label>
    <input type="text" name="username" id="username" class="appearance-none rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
  </div>
  <div class="mt-5">
    <label for="password">Mot de passe</label>
    <input type="password" name="password" id="password" class="appearance-none rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
  </div>
  <input type="hidden" name="_csrf_token" value="<?= $vars['_csrf_token'] ?>">
  <div class="mt-5 w-full">
    <button type="submit" class="appearance-none rounded-md block w-full px-3 py-2 bg-indigo-500 text-white">Se connecter</button>
  </div>
</form>
</body>
</html>