<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php if (verifier('twitter_tweeter')): ?>
    <?php echo $view->render('ZcoTwitterBundle::tabs.html.php', array('mentions'   => $mentions, 'currentTab' => 'index')) ?>
<?php else: ?>
    <h1>Derniers <em>tweets</em></h1>
<?php endif ?>

<p class="good">
    Nous disposons d'un compte <a href="http://twitter.com">Twitter</a>, auquel
    vous pouvez vous abonner pour vous tenir au courant de l'actualité du site.
    Si vous souhaitez partager une information en rapport avec le thème du site sur notre compte
    Twitter, vous pouvez contacter un administrateur ou un rédacteur par message privé afin de
    lui proposer votre <em>tweet</em>.
</p>

<?php if (!count($tweets)): ?>
    <p><em>Il n'y a actuellement aucun tweet.</em></p>
<?php else: ?>
    <?php echo $view['knp_pagination']->render($tweets) ?>
    
    <div class="center">
        <form method="get" action="<?php echo $view['router']->path('zco_search_index', ['section' => 'twitter']) ?>">
            <input type="text" class="input-xxlarge" name="recherche" placeholder="Rechercher parmi tous les tweets publiés…" />
            <noscript><input type="submit" class="btn" value="Rechercher" /></noscript>
        </form>
    </div>

    <?php foreach ($tweets as $tweet): ?>
        <?php echo $view->render('ZcoTwitterBundle::tweet.html.php', compact('tweet')) ?>
    <?php endforeach ?>

    <?php if (count($tweets) > 7): ?>
        <?php echo $view['knp_pagination']->render($tweets) ?>
    <?php endif ?>
<?php endif ?>
