<?php $view->extend('::layouts/default.html.php') ?>

<h1>Statistiques de <?php echo htmlspecialchars($Stats['infos']['utilisateur_pseudo']) ?></h1>

<p class="centre">
	<?php if(!empty($Stats['infos']['utilisateur_avatar'])){ ?>
	<img src="/uploads/avatars/<?php echo htmlspecialchars($Stats['infos']['utilisateur_avatar']) ?>" alt="Avatar" /><br />
	<?php } ?>

	Vous visualisez le rapport d'activité de <strong><a href="/membres/profil-<?php echo $Stats['infos']['utilisateur_id'] ?>-<?php echo rewrite($Stats['infos']['utilisateur_pseudo']) ?>.html"><?php echo htmlspecialchars($Stats['infos']['utilisateur_pseudo']) ?></a></strong>.
</p>

<h2>Activité globale</h2>

<ul type="disc">
	<li>Au total, ce zCorrecteur a réalisé <strong><?php echo $Stats['nombre_total_corrections'] ?></strong> correction<?php echo pluriel($Stats['nombre_total_corrections']) ?> :
	<ul type="square">
		<li><strong><?php echo $Stats['nombre_total_mini_tutos'] ?></strong> mini-tuto<?php echo pluriel($Stats['nombre_total_mini_tutos']) ?>.
			<ul type="circle">
				<li><strong><?php echo $Stats['nombre_corrections_mini_tutos'] ?></strong> correction<?php echo pluriel($Stats['nombre_corrections_mini_tutos']) ?>.</li>
				<li><strong><?php echo $Stats['nombre_recorrections_mini_tutos'] ?></strong> recorrection<?php echo pluriel($Stats['nombre_recorrections_mini_tutos']) ?>.</li>
			</ul>
		</li>
		<li><strong><?php echo $Stats['nombre_total_big_tutos'] ?></strong> big-tutos.
			<ul type="circle">
				<li><strong><?php echo $Stats['nombre_corrections_big_tutos'] ?></strong> correction<?php echo pluriel($Stats['nombre_corrections_big_tutos']) ?>.</li>
				<li><strong><?php echo $Stats['nombre_recorrections_big_tutos'] ?></strong> recorrection<?php echo pluriel($Stats['nombre_recorrections_big_tutos']) ?>.</li>
			</ul>
		</li>
	</ul></li>
	<li>Soit, en tout :
		<ul type="circle">
			<li><strong><?php echo $Stats['nombre_corrections'] ?></strong> correction<?php echo pluriel($Stats['nombre_corrections']) ?>.</li>
			<li><strong><?php echo $Stats['nombre_recorrections'] ?></strong> recorrection<?php echo pluriel($Stats['nombre_recorrections']) ?>.</li>
		</ul></li>
</ul>

<?php if(verifier('voir_tutos_corriges')){ ?>
<p>
	<img src="/img/membres/corrections.png" alt="" />
	<a href="/zcorrection/corrections.html?zcorrected=1&zco=<?php echo $Stats['infos']['utilisateur_pseudo'] ?>">
		Voir toutes ses corrections
	</a>
</p>
<?php } ?>
<br />

<h2>Temps de correction</h2>

<div class="rmq attention">Les tutoriels ayant fait l'objet d'un abandon par son précédent correcteur ne sont pas comptabilisés dans ses statistiques, afin de ne pas les fausser.</div>

<ul type="disc">
	<li>Le temps moyen de correction global de ce zCorrecteur est de <strong><?php echo $Stats['temps_moyen_correction_global'] ?></strong> :
	<ul type="circle">
		<li>Le temps moyen de correction de mini-tutos est de <strong><?php echo $Stats['temps_moyen_correction_mini'] ?></strong>.</li>
		<li>Le temps moyen de correction de big-tutos est de <strong><?php echo $Stats['temps_moyen_correction_big'] ?></strong>.</li>
	</ul></li>
	<li>Le temps moyen de recorrection global de ce zCorrecteur est de <strong><?php echo $Stats['temps_moyen_recorrection_global'] ?></strong> :
	<ul type="circle">
		<li>Le temps moyen de recorrection de mini-tutos est de <strong><?php echo $Stats['temps_moyen_recorrection_mini'] ?></strong>.</li>
		<li>Le temps moyen de recorrection de big-tutos est de <strong><?php echo $Stats['temps_moyen_recorrection_big'] ?></strong>.</li>
	</ul></li>
</ul>
<br />

<h2>Activité sur l'année</h2>

<ul type="disc">
	<li>Ces douze derniers mois, ce zCorrecteur a effectué <strong><?php echo $Stats['nombre_total_corrections_12_mois'] ?></strong> corrections.
	<ul type="square">
		<li><strong><?php echo $Stats['nombre_corrections_12_mois'] ?></strong> correction<?php echo pluriel($Stats['nombre_corrections_12_mois']) ?>.</li>
		<li><strong><?php echo $Stats['nombre_recorrections_12_mois'] ?></strong> recorrection<?php echo pluriel($Stats['nombre_recorrections_12_mois']) ?>.</li>
	</ul></li>
</ul>

<p class="centre">
	<a href="/membres/profil-<?php echo $Stats['infos']['utilisateur_id'] ?>-<?php echo rewrite($Stats['infos']['utilisateur_pseudo']) ?>.html">
		Retour au profil de <strong><?php echo htmlspecialchars($Stats['infos']['utilisateur_pseudo']) ?></strong>.
	</a>
</p>
