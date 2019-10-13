<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Valider un billet</h1>

<form method="post" action="">
    <p>
        Êtes-vous sûr de vouloir valider ce billet
        ce billet ayant pour titre
        <strong><?php echo htmlspecialchars($InfosBillet['version_titre']) ?></strong> ?
    </p>

    <?php if(!is_null($InfosBillet['blog_date_publication']) && $InfosBillet['blog_date_publication'] != '0000-00-00 00:00:00'){ ?>
    <p>
        <input type="checkbox" name="conserver_date_pub" id="conserver_date_pub" />
        <label for="conserver_date_pub">
            Conserver la date de publication indiquée (<strong><?php echo dateformat($InfosBillet['blog_date_publication'], MINUSCULE); ?></strong>).
        </label>
    </p>
    <?php } ?>
    <p class="form-actions center">
        <input type="submit" class="btn btn-primary" value="Oui" />
        <a class="btn" href="<?php echo $view['router']->path('zco_blog_show', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre'])]) ?>">Non</a>
    </p>
</form>
