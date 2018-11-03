<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="Content-Language" content="fr"/>
    <meta name="language" content="fr"/>
    <meta http-equiv="content-language" content="fr"/>
    <meta name="description" content="<?php echo Page::$description; ?>"/>
    <meta name="robots" content="<?php echo Page::$robots; ?>"/>
    <?php $view['slots']->output('meta') ?>

    <title><?php echo str_replace(array(' '), ' ', Page::$titre); ?></title>

    <?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/global.css') ?>
    <?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/js/global.js') ?>

    <?php foreach ($view['vitesse']->stylesheets() as $assetUrl): ?>
        <link rel="stylesheet" href="<?php echo $assetUrl ?>" media="screen" type="text/css"/>
    <?php endforeach ?>
    <!--[if IE]>
    <?php foreach ($view['vitesse']->stylesheets(array('@ZcoCoreBundle/Resources/public/css/ie.css')) as $assetUrl): ?>
    <link rel="stylesheet" href="<?php echo $assetUrl ?>" media="screen" type="text/css"/>
    <?php endforeach ?>
    <![endif]-->

    <?php foreach ($view['vitesse']->javascripts(array('mootools', 'mootools-more')) as $assetUrl): ?>
        <script type="text/javascript" src="<?php echo $assetUrl ?>"></script>
    <?php endforeach ?>

    <link rel="alternate" type="application/atom+xml" title="Derniers billets du blog" href="/blog/flux.html" />

    <link rel="icon" type="image/png" href="/favicon.png"/>
    <link rel="start" title="zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !"
          href="/"/>
</head>

<body>
<div id="acces_rapide">
    <a href="#page">Aller au menu</a> -
    <a href="#content">Aller au contenu</a>
</div>

<div id="body">
    <?php if (empty($xhr)): ?>
    <div id="header">
        <div id="title">
            <div id="title-oreilles">
                <a href="/"
                   title="zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !">
                    zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !
                </a>
            </div>
            <div id="title-zcorrecteurs">
                <a href="/"
                   title="zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !">
                    zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !
                </a>
            </div>

            <?php echo $randomQuoteHtml ?>
        </div>
    </div>

    <div id="speedbarre">
        <ul class="nav">
            <li>
                <a href="<?php echo $view['router']->path('zco_home') ?>">Accueil</a>
            </li>
            <li>
                <a href="/blog/">Blog</a>
            </li>
            <li>
                <a href="/forum/">Forum</a>
            </li>
            <li>
                <a href="<?php echo $view['router']->path('zco_quiz_index') ?>">Quiz</a>
            </li>
            <li>
                <a href="/dictees/">Dictées</a>
            </li>
            <?php if (verifier('connecte')): ?>
                <li>
                    <a href="/mp/">
                        <?php if ($_SESSION['MPsnonLus'] > 0): ?>
                            <?php echo $_SESSION['MPsnonLus'] ?> message<?php echo pluriel($_SESSION['MPsnonLus']) ?>
                        <?php else: ?>
                            Messagerie
                        <?php endif ?>
                    </a>
                </li>
            <?php endif ?>
            <?php if (verifier('admin')): ?>
                <li>
                    <a href="<?php echo $view['router']->path('zco_admin_index') ?>">
                        Admin
                        <?php if ($adminCount > 0): ?>(<?php echo $adminCount ?>)<?php endif ?>
                    </a>
                </li>
            <?php endif ?>
            <?php if (!verifier('connecte')): ?>
                <li>
                    <a href="<?php echo $view['router']->path('zco_user_session_login') ?>">Connexion</a>
                </li>
                <li>
                    <a href="<?php echo $view['router']->path('zco_user_session_register') ?>">Inscription</a>
                </li>
            <?php endif ?>
        </ul>

        <div class="liens_droite">
            <form class="navbar-search form-search"
                  id="search"
                  method="get"
                  action="<?php echo $view['router']->path('zco_search_index', ['section' => $searchSection]) ?>">
                <input type="text"
                       name="recherche"
                       id="recherche"
                       class="search search-query pull-left"
                       placeholder="Rechercher…"/>
                <input type="submit" class="submit" value="Rechercher" style="display:none"/>
            </form>
        </div>
    </div>

    <div id="page">
        <div class="sidebar sidebarleft">
            <?php if (verifier('connecte')): ?>
                <div class="bloc moncompte">
                    <h4>Mon compte</h4>
                    <ul class="nav nav-list">
                        <li class="first">
                            <a href="<?php echo $view['router']->path('zco_user_profile', ['id' => $_SESSION['id'], 'slug' => rewrite($_SESSION['pseudo'])]) ?>">
                                <?php echo htmlspecialchars($_SESSION['pseudo']) ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $view['router']->path('zco_options_index') ?>">
                                Mes options
                            </a>
                        </li>
                        <li>
                            <a href="/blog/mes-billets.html">
                                Mes billets
                            </a>
                        </li>
                        <li>
                            <a href="/dictees/proposer.html">Mes dictées</a>
                        </li>
                        <li class="last">
                            <a href="<?php echo $view['router']->path('zco_user_session_logout', ['token' => $_SESSION['token']]) ?>"
                               rel="Cliquez ici pour vous déconnecter."
                               title="Déconnexion">
                                Déconnexion
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif ?>

            <div class="bloc communaute">
                <h4>Communauté</h4>
                <ul class="nav nav-list">
                    <li class="first">
                        <a href="/recrutement/" style="font-weight: bold;">
                            Nous rejoindre
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $view['router']->path('zco_donate_index') ?>">
                            Faire un don
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $view['router']->path('zco_about_team') ?>">
                            L'équipe
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $view['router']->path('zco_user_index') ?>">
                            Membres
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div id="content" class="right">
            <?php endif ?>
            <?php
            /* Affichage des messages éventuels en haut de la page */
            if (!empty($_SESSION['erreur'])) {
                foreach ($_SESSION['erreur'] as $erreur)
                    echo '<p class="UI_infobox">' . $erreur . '</p>';
                $_SESSION['erreur'] = array();
            }

            if (!empty($_SESSION['message'])) {
                foreach ($_SESSION['message'] as $message)
                    echo '<p class="UI_infobox">' . $message . '</p>';
                $_SESSION['message'] = array();
            } ?>

            <?php if (empty($xhr)): ?>
                <div id="postloading-area"></div>

                <p class="arianne">
                    Vous êtes ici : <?php echo implode(' &gt; ', \Page::$fil_ariane) ?>
                </p>
            <?php endif ?>

            <?php $view['slots']->output('_content') ?>

            <?php if (empty($xhr)): ?>
        </div>
    </div>
<?php echo $view->render('::layouts/_footer.html.php') ?>
<?php endif ?>

    <?php foreach ($view['vitesse']->javascripts() as $assetUrl): ?>
        <script type="text/javascript" src="<?php echo $assetUrl ?>"></script>
    <?php endforeach ?>
    <script type="text/javascript"
            src="<?php echo $view['router']->path('fos_js_routing_js', array('callback' => 'fos.Router.setData')) ?>"></script>
    <?php echo $view['javelin']->renderHTMLFooter() ?>
</div>
</body>
</html>