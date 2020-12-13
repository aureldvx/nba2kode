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
<body class="page-listing">
<?php
include_once __TEMPLATES_DIR__ . 'components/notifications.php'
?>
<header class="flex items-center justify-between p-4 shadow-md fixed w-full bg-white">
  <h1 class="text-2xl font-bold text-indigo-500">Éditer un match</h1>
  <div class="actions">
    <a href="/admin/" class="appearance-none rounded-sm px-4 py-3 bg-indigo-500 text-white">Retour au listing</a>
  </div>
</header>

<main class="pt-44">
  <form action="<?= str_replace('/index.php', '', $_SERVER['PHP_SELF']) ?>" method="post" class="ml-auto mr-auto rounded-md shadow-lg flex flex-col items-stretch w-5/12 p-5">
    <div>
      <label for="play_date">Date du match</label>
      <input type="datetime-local" name="play_date" id="play_date" value="<?= (new DateTime($vars['actual']->play_date))->format('Y-m-d\TH:i') ?>" class="appearance-none rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
    </div>
    <fieldset class="mt-10">
      <legend class="font-bold text-xl">Domicile</legend>
      <div class="mt-5">
        <label for="home_team">Équipe</label>
        <select name="home_team" id="home_team" class="rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
            <?php
            foreach ($vars['teams'] as $team) : ?>
              <option value="<?= $team ?>" <?= $team === $vars['actual']->teams_home_name ? 'selected' : '' ?>><?= $team ?></option>
            <?php
            endforeach; ?>
        </select>
      </div>
      <div class="mt-5">
        <label for="home_score">Score</label>
        <input type="number" name="home_score" id="home_score" value="<?= $vars['actual']->score_home_total ?>" class="appearance-none rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
      </div>
    </fieldset>
    <fieldset class="mt-10">
      <legend class="font-bold text-xl">Extérieur</legend>
      <div class="mt-5">
        <label for="away_team">Équipe</label>
        <select name="away_team" id="away_team" class="rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
            <?php
            foreach ($vars['teams'] as $team) : ?>
              <option value="<?= $team ?>" <?= $team === $vars['actual']->teams_away_name ? 'selected' : '' ?>><?= $team ?></option>
            <?php
            endforeach; ?>
        </select>
      </div>
      <div class="mt-5">
        <label for="away_score">Score</label>
        <input type="number" name="away_score" id="away_score" value="<?= $vars['actual']->score_home_total ?>" class="appearance-none rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
      </div>
    </fieldset>
    <input type="hidden" name="_csrf_token" value="<?= $vars['_csrf_token'] ?>">
    <input type="hidden" name="match_id" value="<?= $vars['actual']->id ?>">
    <div class="mt-5 w-full">
      <button type="submit" class="appearance-none rounded-md block w-full px-3 py-2 bg-indigo-500 text-white">Sauvegarder les modifications</button>
    </div>
  </form>
</main>

</body>
</html>