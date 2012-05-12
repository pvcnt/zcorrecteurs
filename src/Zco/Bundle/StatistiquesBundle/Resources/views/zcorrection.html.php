<?php $view->extend('::layouts/default.html.php') ?>

<style type="text/css">
.stats {
	float: left;
}

.stats_bloc {
	clear: left;
}

.stats_gauche {
	float: left;
	margin-right: 20px;
	margin-top: 80px;
}

.stats_droite {
	float: left;
}
</style>

<h1>Statistiques de zCorrection</h1>

<?php if(verifier('voir_rapport_zcorr')){ ?>
<p class="gras centre">
	<a href="activite.html">Voir le rapport d'activité des zCorrecteurs</a>
</p>
<?php } ?>

<?php
$url_mini_big = urlencode('/vendor/graph_flash/genere_xml.php?type=mini_big&mini='.$Stats['nombre_total_mini_tutos'].'&big='.$Stats['nombre_total_big_tutos']);
$mini_big_width = 300;
$mini_big_height = 225;
?>
<div class="stats">
<div class="stats_bloc">
	<ul type="disc" class="stats_gauche">
		<li>Au total, il y a <strong><?php echo $Stats['nombre_total_tutos']; ?></strong> tutoriels dont :
		<ul type="square">
			<li><strong><?php echo $Stats['nombre_total_mini_tutos']; ?></strong> mini-tutos.</li>
			<li><strong><?php echo $Stats['nombre_total_big_tutos']; ?></strong> big-tutos.</li>
		</ul></li>
	</ul>

	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="<?php echo $mini_big_width; ?>" height="<?php echo $mini_big_height; ?>" id="Pie3D" class="stats_droite">
		<param name="movie" value="/vendor/graph_flash/fichiers_modele/FCF_Pie3D.swf" />
		<param name="FlashVars" value="&dataURL=<?php echo $url_mini_big; ?>&chartWidth=<?php echo $mini_big_width; ?>&chartHeight=<?php echo $mini_big_height; ?>">
		<param name="quality" value="high" />
		<embed src="/vendor/graph_flash/fichiers_modele/FCF_Pie3D.swf" flashVars="&dataURL=<?php echo $url_mini_big; ?>&chartWidth=<?php echo $mini_big_width; ?>&chartHeight=<?php echo $mini_big_height; ?>" quality="high" width="<?php echo $mini_big_width; ?>" height="<?php echo $mini_big_height; ?>" name="Pie3D" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
</div>

<?php
$url_temps_moyen = urlencode('/vendor/graph_flash/genere_xml.php?type=temps_moyen&tmcg='.$sec_tmcg.'&tmcm='.$sec_tmcm.'&tmcb='.$sec_tmcb.'&tmrg='.$sec_tmrg.'&tmrm='.$sec_tmrm.'&tmrb='.$sec_tmrb);
$temps_moyen_width = 400;
$temps_moyen_height = 300;
?>

<div class="stats_bloc">
	<ul type="disc" class="stats_gauche">
		<li>Le temps moyen de correction global est de <strong><?php echo $Stats['temps_moyen_correction_global']; ?></strong> :
		<ul type="square">
			<li>Le temps moyen de correction de mini-tutos est de <strong><?php echo $Stats['temps_moyen_correction_mini']; ?></strong>.</li>
			<li>Le temps moyen de correction de big-tutos est de <strong><?php echo $Stats['temps_moyen_correction_big']; ?></strong>.</li>
		</ul></li>
		<li>Le temps moyen de recorrection global est de <strong><?php echo $Stats['temps_moyen_recorrection_global']; ?></strong> :
		<ul type="square">
			<li>Le temps moyen de recorrection de mini-tutos est de <strong><?php echo $Stats['temps_moyen_recorrection_mini']; ?></strong>.</li>
			<li>Le temps moyen de recorrection de big-tutos est de <strong><?php echo $Stats['temps_moyen_recorrection_big']; ?></strong>.</li>
		</ul></li>
	</ul>

	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="<?php echo $temps_moyen_width; ?>" height="<?php echo $temps_moyen_height; ?>" id="Pie3D" class="stats_droite">
		<param name="movie" value="/vendor/graph_flash/fichiers_modele/FCF_MSColumn3D.swf" />
		<param name="FlashVars" value="&dataURL=<?php echo $url_temps_moyen; ?>&chartWidth=<?php echo $temps_moyen_width; ?>&chartHeight=<?php echo $temps_moyen_height; ?>">
		<param name="quality" value="high" />
		<embed src="/vendor/graph_flash/fichiers_modele/FCF_MSColumn3D.swf" flashVars="&dataURL=<?php echo $url_temps_moyen; ?>&chartWidth=<?php echo $temps_moyen_width; ?>&chartHeight=<?php echo $temps_moyen_height; ?>" quality="high" width="<?php echo $temps_moyen_width; ?>" height="<?php echo $temps_moyen_height; ?>" name="Pie3D" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
</div>
<?php
$url_co_reco = urlencode('/vendor/graph_flash/genere_xml.php?type=co_reco&co='.$Stats['nombre_total_corrections'].'&reco='.$Stats['nombre_total_recorrections']);
$co_reco_width = 300;
$co_reco_height = 225;
?>
<div class="stats_bloc">
	<ul type="disc" class="stats_gauche">
		<li>Au total, <strong><?php echo $Stats['nombre_total_corrections']; ?></strong> tutoriels ont été corrigés.</li>
		<li>dont <strong><?php echo $Stats['nombre_total_recorrections']; ?></strong> nécessitant une recorrection.</li>
	</ul>

	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="<?php echo $co_reco_width; ?>" height="<?php echo $co_reco_height; ?>" id="Pie3D" class="stats_droite">
		<param name="movie" value="/vendor/graph_flash/fichiers_modele/FCF_Pie3D.swf" />
		<param name="FlashVars" value="&dataURL=<?php echo $url_co_reco; ?>&chartWidth=<?php echo $co_reco_width; ?>&chartHeight=<?php echo $co_reco_height; ?>">
		<param name="quality" value="high" />
		<embed src="/vendor/graph_flash/fichiers_modele/FCF_Pie3D.swf" flashVars="&dataURL=<?php echo $url_co_reco; ?>&chartWidth=<?php echo $co_reco_width; ?>&chartHeight=<?php echo $co_reco_height; ?>" quality="high" width="<?php echo $co_reco_width; ?>" height="<?php echo $co_reco_height; ?>" name="Pie3D" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
</div>

<div id="stats_bloc">
	<ul type="disc" class="stats_gauche">
	<li>La moyenne du nombre de corrections + recorrections par zCorrecteur est de <strong><?php echo $view['humanize']->numberformat($Stats['nombre_moyen_global_par_zco']); ?></strong>.</li>
	</ul>
</div>
</div>
