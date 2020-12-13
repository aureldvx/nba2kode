<?php

if (! defined('__APP_SECRET__')) {
    header('Location: /');
    die();
}
?>
<!doctype html>
<html lang="fr">
<head>
    <?php
    include_once __TEMPLATES_DIR__ . 'layout/head.php';
    ?>
  <title>Listing des matchs</title>
</head>
<body class="w-full font-sans">
<?php
include_once __TEMPLATES_DIR__ . 'components/notifications.php'
?>
<header class="flex items-center justify-between p-4 shadow-md fixed w-full bg-white">
  <h1 class="text-2xl font-bold text-indigo-500">Liste des matchs</h1>
  <div class="actions">
    <a href="/admin/generate-key" class="appearance-none rounded-sm px-4 py-3 bg-indigo-500 text-white">Générer une clé d'API</a>
    <a href="/admin/create" class="appearance-none rounded-sm px-4 py-3 bg-indigo-500 text-white">Ajouter un match</a>
  </div>
</header>
<main class="pt-44">
  <table class="w-11/12 ml-auto mr-auto">
  <tbody>
  <?php foreach ($vars['matches'] as $match) : ?>
  <tr class="h-15">
    <td class="p-2"><?= $match->id ?></td>
    <td class="p-2"><span><?= (new DateTime($match->play_date))->format('d M Y H\hi') ?></span></td>
    <td class="p-2">
      <img src="<?= $match->teams_home_logo ?>" width="30" height="30" alt="" style="object-fit: contain; object-position: center;">
    </td>
    <td class="p-2"><span><?= $match->teams_home_name ?></span></td>
    <td class="p-2 text-right font-bold"><?= $match->score_home_total ?></td>
    <td class="p-2 font-bold"><?= $match->score_away_total ?></td>
    <td class="p-2"><span><?= $match->teams_away_name ?></span></td>
    <td class="p-2">
      <img src="<?= $match->teams_away_logo ?>" width="30" height="30" alt="" style="object-fit: contain; object-position: center;">
    </td>
    <td class="p-2">
      <a href="/admin/edit?match=<?= $match->id ?>" class="appearance-none rounded-sm px-3 py-2 bg-yellow-50 text-yellow-500">Modifier</a>
      <a href="/admin/delete?match=<?= $match->id ?>" class="appearance-none rounded-sm px-3 py-2 bg-red-50 text-red-500">Supprimer</a>
    </td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</main>
</body>
</html>