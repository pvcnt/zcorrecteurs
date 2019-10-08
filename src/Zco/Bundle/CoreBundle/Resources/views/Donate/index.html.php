<?php $view->extend('::layouts/bootstrap.html.php') ?>

<div style="float: right; width: 340px;">
    <?php echo $view->render('ZcoCoreBundle:Donate:_menu.html.php', array('chequeOuVirement' => false)) ?>
</div>

<div style="margin-right: 380px;">
    <h1>Faire un don</h1>

    <p class="good">
        Depuis 2008, nous vous proposons sans cesse de nouvelles ressources autour de la
        langue française (articles, quiz, dictées…). Tout comme les corrections (mises en place dès 2006),
        ces services sont gérés par des bénévoles encadrés par
        <a href="/blog/343/zcorrecteurs-fr-donne-naissance-a-une-association">l’association Corrigraphie</a>,
        fondée en 2011.
    </p>

    <p class="good">
        Si les dépenses sont nombreuses, les revenus de l’association proviennent de deux
        sources&nbsp;: les prestations liées à la (très) faible publicité présente sur le site…
        et vos dons, qui sont notre source de revenus la plus fiable et valorisante. En nous
        soutenant, vous nous donnez les moyens de subvenir à nos besoins au quotidien mais
        aussi <em>de nous lancer dans de nouveaux projets</em>.
    </p>

    <p class="good">
        Parmi ceux-ci, nous désirons rendre nos ressources et nos corrections accessibles
        à plus de monde. Pour y parvenir, nous avons besoin de nous faire connaître et de
        nous déplacer ponctuellement pour travailler avec nos différents partenaires.
        Nous avons également des frais liés à la formation des membres ainsi qu'à
        l'organisation des réunions de travail indispensables à la poursuite de notre
        mission.
    </p>

    <h2 id="donateurs">Liste des donateurs</h2>

    <p class="good">
        Voici une liste de tous ceux qui nous ont aidés jusqu’à présent à faire
        vivre le site et à assumer nos frais monétaires. Notez que tous
        n’apparaissent pas forcément dans cette liste, certains ayant préféré rester
        anonymes. Quoi qu'il en soit, nous remercions chaleureusement tous ces membres.
    </p>

    <table class="table">
        <thead>
        <tr>
            <th style="width: 60%;">Donateur</th>
            <th style="width: 20%;">Date</th>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td class="centre">Léo Martinez (the_little)</td>
            <td class="centre">Le 13/03/2012</td>
        </tr>
        <tr>
            <td class="centre">Marc Vandamme (Dalshim)</td>
            <td class="centre">Le 05/10/2011</td>
        </tr>
        <tr>
            <td class="centre">Bp0n0x58</td>
            <td class="centre">Le 16/05/2011</td>
        </tr>
        <tr>
            <td class="centre">the_little</td>
            <td class="centre">Le 09/01/2011</td>
        </tr>
        <tr>
            <td class="centre">aurel2108</td>
            <td class="centre">Le 07/01/2011</td>
        </tr>
        <tr>
            <td class="centre">Tûtie</td>
            <td class="centre">Le 06/01/2011</td>
        </tr>
        <tr>
            <td class="centre">MysticalMarc</td>
            <td class="centre">Le 05/10/2010</td>
        </tr>
        <tr>
            <td class="centre">B. Regnier</td>
            <td class="centre">Le 10/09/2010</td>
        </tr>
        <tr>
            <td class="centre">nr</td>
            <td class="centre">Le 22/02/2010</td>
        </tr>
        <tr>
            <td class="centre">Vyk12</td>
            <td class="centre">Le 12/01/2010</td>
        </tr>
        <tr>
            <td class="centre">christophetd</td>
            <td class="centre">Le 15/11/2009</td>
        </tr>
        <tr>
            <td class="centre">rodolphe23</td>
            <td class="centre">Le 13/11/2009</td>
        </tr>
        <tr>
            <td class="centre">Emurikku</td>
            <td class="centre">Le 26/05/2009</td>
        </tr>
        <tr>
            <td class="centre">Marc Vandamme (Dalshim)</td>
            <td class="centre">Le 25/05/2009</td>
        </tr>
        </tbody>
    </table>

    <p style="font-size: 0.8em;">
        Si vous n’apparaissez pas sur cette liste et souhaitez y figurer, vous
        pouvez <a href="<?php echo $view['router']->path('zco_about_contact', array('objet' => 'Don')) ?>">nous le
            signaler</a>.
    </p>
</div>