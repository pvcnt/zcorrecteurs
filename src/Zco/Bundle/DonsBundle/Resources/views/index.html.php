<?php $view->extend('ZcoDonsBundle::layout.html.php') ?>

<div style="float: right; width: 340px;">
	<?php echo $view->render('ZcoDonsBundle::_menu.html.php', array('chequeOuVirement' => false)) ?>
</div>

<div style="margin-right: 380px;">
	<h1>Faire un don</h1>

	<p class="good">
		Depuis 2008, nous vous proposons sans cesse de nouvelles ressources autour de la 
		langue française (articles, quiz, dictées…). Tout comme les corrections (mises en place dès 2006), 
		ces services sont gérés par des bénévoles encadrés par 
		<a href="/blog/billet-343-zcorrecteurs-fr-donne-naissance-a-une-association.html">l’association Corrigraphie</a>, 
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
			<?php foreach ($dons as $don): ?>
			<tr>
				<td class="centre">
					<?php if (!empty($don['nom'])): ?>
 						<?php echo htmlspecialchars($don['nom']) ?>
						<?php if (!empty($don['utilisateur_id'])) echo '('.$don->Utilisateur.')' ?>
					<?php else: ?>
						<?php echo $don->Utilisateur ?>
					<?php endif; ?>
				</td>
				<td class="centre"><?php echo dateformat($don['date'], DATE); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<p style="font-size: 0.8em;">
		Si vous n’apparaissez pas sur cette liste et souhaitez y figurer, vous
		pouvez <a href="<?php echo $view['router']->generate('zco_about_contact', array('objet' => 'Don')) ?>">nous le signaler</a>.
	</p>
</div>