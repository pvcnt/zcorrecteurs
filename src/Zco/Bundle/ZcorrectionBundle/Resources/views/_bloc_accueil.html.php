<ul>
	<li>Documents envoyés en zCorrection : <?php echo $StatistiquesZcorrection['nombre_total_tutos']; ?>.
		<ul class="tutos_envoyes">
			<li>Mini-tutos : <?php echo $StatistiquesZcorrection['nombre_total_mini_tutos']; ?>.</li>
			<li>Big-tutos : <?php echo $StatistiquesZcorrection['nombre_total_big_tutos']; ?>.</li>
		</ul>
	</li>
	<li>
		Nombre moyen de <acronym title="Le terme de « correction » comprend la correction et la recorrection.">corrections</acronym>
		par zCorrecteur : <?php echo $view['humanize']->numberformat($StatistiquesZcorrection['nombre_moyen_global_par_zco']); ?>.
	</li>
	<li>
		État du service de zCorrection :
		<?php if($NombreTutosAttente < 5) echo '<span class="vertf">peu de documents en attente</span>';
		elseif($NombreTutosAttente < 10) echo '<span class="orange">moins de 10 documents en attente</span>';
		else echo '<span class="rouge">beaucoup de documents en attente</span>'; ?>.
	</li>
</ul>