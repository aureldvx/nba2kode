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
    <h1>Ajouter un match</h1>
    <div class="actions">
        <a href="/admin/">Retour au listing</a>
    </div>
</header>

<main>
    <form action="<?= str_replace('/index.php', '', $_SERVER['PHP_SELF']) ?>" method="post">
        <div class="form-field">
            <label for="play_date">Date du match</label>
            <input type="datetime-local" name="play_date" id="play_date">
        </div>
        <fieldset>
            <legend>Domicile</legend>
            <div class="form-field">
                <label for="home_team">Équipe</label>
                <select name="home_team" id="home_team">
                    <?php foreach ($vars['teams'] as $team) : ?>
                        <option value="<?= $team ?>"><?= $team ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-field">
                <label for="home_score">Score</label>
                <input type="number" name="home_score" id="home_score">
            </div>
        </fieldset>
        <fieldset>
            <legend>Extérieur</legend>
            <div class="form-field">
                <label for="away_team">Équipe</label>
                <select name="away_team" id="away_team">
                    <?php foreach ($vars['teams'] as $team) : ?>
                        <option value="<?= $team ?>"><?= $team ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-field">
                <label for="away_score">Score</label>
                <input type="number" name="away_score" id="away_score">
            </div>
        </fieldset>
        <input type="hidden" name="_csrf_token" value="<?= $vars['_csrf_token'] ?>">
        <button type="submit">Créer le match</button>
    </form>
</main>

</body>
</html>