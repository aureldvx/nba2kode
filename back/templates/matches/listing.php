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
<header>
  <h1>Liste des matchs</h1>
  <div class="actions">
    <a href="/admin/generate-key">Générer une clé d'API</a>
    <a href="/admin/create">Ajouter un match</a>
  </div>
</header>

<table>
  <tbody>
  <?php foreach ($vars['matches'] as $match) : ?>
  <tr>
    <td><?= $match->id ?></td>
    <td><span><?= (new DateTime($match->play_date))->format('d M Y H\hi') ?></span></td>
    <td>
      <img src="<?= $match->teams_home_logo ?>" width="30" height="30" alt="" style="object-fit: contain; object-position: center;">
    </td>
    <td><span><?= $match->teams_home_name ?></span></td>
    <td><?= $match->score_home_total ?></td>
    <td><?= $match->score_away_total ?></td>
    <td><span><?= $match->teams_away_name ?></span></td>
    <td>
      <img src="<?= $match->teams_away_logo ?>" width="30" height="30" alt="" style="object-fit: contain; object-position: center;">
    </td>
    <td>
      <a href="/admin/edit?match=<?= $match->id ?>">Modifier</a>
      <a href="/admin/delete?match=<?= $match->id ?>">Supprimer</a>
    </td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</body>
</html>