<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoPagesBundle:Static:aboutTabs.html.php', array('currentTab' => 'index')) ?>

<h1>Les zCorrecteurs, toute une histoire.</h1>

<div class="row-fluid">
    <h2>La genèse de la zCorrection</h2>

    <p class="good">
        Tout a commencé en novembre 2006 lorsque JALeX a décidé de constituer une
        équipe de correcteurs capable de corriger le contenu d’un célèbre site
        d’informatique, le «&nbsp;Site du Zéro&nbsp;». Après un premier appel à candidatures
        (jugez plutôt, 250 candidatures avaient été reçues !), les noms des 16
        premiers «&nbsp;zCorrecteurs&nbsp;» furent énoncés. Il s’agissait de Petrus 6, Keristos,
        Dark elfeid, WhiteCoyote, Klouguinette, Nephthys, Ziame, dworkin, Manene,
        Moejul, Seb087, Scroux, Delete, ptipilou, Jmbe et koubizz.
    </p>
</div>

<p class="good">
    Bien entendu, depuis beaucoup sont partis, et de l’équipe d’origine, il n’en
    reste plus beaucoup, mais de nouvelles recrues sont venues remplacer les
    départs par la suite.
</p>

<h2>Un site en constante évolution</h2>

<p class="good">
    Après un bon mois sans aucune interface pour travailler, un premier site fut
    mis en ligne permettant aux auteurs de soumettre leurs documents. Puis après
    un nouveau design réalisé par Moejul, le projet de se doter d’un site plus
    riche fut lancé.</p>

<p class="good">
    C’est dworkin, un des premiers zCorrecteurs, qui avec ptipilou et Ziame a
    suivi et géré tout le projet, de sa conception jusqu’à la fin de sa réalisation.
    D’autres développeurs volontaires et bénévoles sont alors venus nous aider
    (DJ Fox, Savageman et vincent1870, et plus tard .skaz pour le design). Ils sont
    ceux qui ont développé la très grande partie de ce site, qui est le résultat
    d’une année de développement initial et depuis de mises à jours continues.
</p>

<h2>La zCorrection aujourd’hui</h2>

<p class="good">
	La qualité du travail et des conseils prodigués par les premiers bénévoles
	ayant rapidement été reconnue par les auteurs, il a été décidé d’aller plus
	loin et de proposer en plus des ressources en rapport avec notre belle langue.
	C’est ainsi que nous pouvons aujourd’hui vous proposer un contenu riche et
	varié : des <a href="/blog/">articles</a> sur des points précis de langue,
	des <a href="<?php echo $view['router']->path('zco_quiz_index') ?>">quiz</a> et <a href="/dictees/">dictées</a> pour tester
	et améliorer vos connaissances ainsi qu’un <a href="/forum/">forum</a>
	où il y aura toujours une bonne âme pour répondre à vos questions.
</p>

<p class="good">
    En parallèle, nos activités de correction se poursuivent toujours aujourd’hui,
    même si nous corrigeons maintenant pour d’autres sites, mais aussi pour des
    particuliers ou des associations. Cela nous permet de toucher toujours plus
    de personnes et d’exercer la démarche de <em><strong>pédagogie</strong></em>
    qui constitue notre marque de fabrique.
</p>
