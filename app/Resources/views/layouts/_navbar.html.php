<div class="navbar-inner">
    <div class="container">
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
            <li><a href="/dictees/">Dictées</a></li>
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
                        <?php if ($adminCount > 0): ?>
                            <span class="badge"><?php echo $adminCount ?></span>
                        <?php endif ?>
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

        <?php if (verifier('connecte')): ?>
            <ul class="nav pull-right">
                <li class="dropdown first last">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        Mon compte <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo $view['router']->path('zco_user_profile', ['id' => $_SESSION['id'], 'slug' => rewrite($_SESSION['pseudo'])]) ?>">
                                <?php echo htmlspecialchars($_SESSION['pseudo']) ?>
                            </a>
                        </li>
                        <li class="divider">&nbsp;</li>
                        <li>
                            <a href="<?php echo $view['router']->path('zco_options_index') ?>">Mes paramètres</a>
                        </li>
                        <li>
                            <a href="/blog/mes-billets.html">Mes billets</a>
                        </li>
                        <li>
                            <a href="/dictees/proposer.html">Mes dictées</a>
                        </li>
                        <li class="divider">&nbsp;</li>
                        <li class="last">
                            <a href="<?php echo $view['router']->path('zco_user_session_logout', ['token' => $_SESSION['token']]) ?>">
                                Déconnexion
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        <?php endif ?>

        <form class="navbar-search pull-right form-search"
              id="search"
              method="get"
              action="<?php echo $view['router']->path('zco_search_index', ['section' => $searchSection]) ?>">
            <input type="text" name="recherche" id="recherche" class="search search-query pull-left"
                   placeholder="Rechercher…"/>
            <input type="submit" class="submit" value="Rechercher" style="display:none"/>
        </form>
    </div>
</div>