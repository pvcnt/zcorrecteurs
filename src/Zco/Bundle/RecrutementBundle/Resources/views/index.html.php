<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoRecrutementBundle::_onglets.html.php') ?>

<div class="recrutement_boite">

<table style="float: right; margin-left: 20px;">
	<?php foreach ($equipe as $i => $membre): ?>
	<?php if (($i % 8) === 0): ?>
		<?php if ($i > 0): ?></tr><?php endif; ?>
		<tr>
	<?php endif; ?>
	<td class="center">
		<img src="/uploads/avatars/<?php echo htmlspecialchars($membre['avatar']) ?>" alt="<?php echo htmlspecialchars($membre['pseudo']) ?>" title="<?php echo htmlspecialchars($membre['pseudo']) ?>" class="team-avatar" />
	</td>
	<?php endforeach; ?>
	</tr>
</table>
<?php $view['javelin']->initBehavior('twipsy', array('selector' => '.team-avatar')) ?>

<h1>Faites vivre la langue française !</h1>
<p class="intro-text">
	Nous sommes en permanence à la recherche de passionnés 
	pour compléter notre équipe de bénévoles dans tous les domaines.
</p>

<p class="lien"><a href="liste.html">En savoir plus&nbsp;&raquo;</a></p>

<div style="clear: right;">&nbsp;</div>
</div>

<h2>Nous recherchons en permanence…</h2>

<table class="recrutement_postes">
	<tr>
		<td><a href="/recrutement/recrutement-12-integrez-l-equipe-des-zcorrecteurs.html">
			<span class="titre">Correcteurs</span><br />
			<span class="description">Corriger des documents et aider les auteurs</span>
		</a></td>
		<td><a href="/recrutement/recrutement-14-rejoignez-nos-valeureux-developpeurs.html">
			<span class="titre">Développeurs</span><br />
			<span class="description">Faire évoluer le site en continu</span>
		</a></td>
		<td><a href="/recrutement/recrutement-13-devenez-redacteur-sur-le-site.html">
			<span class="titre">Rédacteurs</span><br />
			<span class="description">Rédiger et valider le contenu du site</span>
		</a></td>
		<td><a href="http://www.corrigraphie.org/adherer">
			<span class="titre">Bénévoles associatifs</span><br />
			<span class="description">Participer à la vie et la croissance de l’association</span>
		</a></td>
	</tr>
</table>

<?php $view['vitesse']->requireResource('@ZcoRecrutementBundle/Resources/public/css/recrutement.css') ?>