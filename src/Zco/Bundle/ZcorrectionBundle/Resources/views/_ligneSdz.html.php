<?php
$liste_types = array(MINI_TUTO => 'Mini', BIG_TUTO => 'Big');
$titre = '';
$description = trim($s['soumission_description']);
if (empty($description))
{
	$description = 'Le valido n\'a laissé aucun message.';
}
else
{
    $description = $view['messages']->parse($description);
}
$description = '<em>Commentaire du validateur :</em><br />'.$description;

if (!empty($s['soumission_commentaire']))
	$description .= '<hr /><em>Commentaire du correcteur :</em><br />'.parse($s['soumission_commentaire']);

if (MINI_TUTO == $s['soumission_type_tuto'])
{
	$titre = $s['mini_tuto_titre'];
	$voir = 'mini';
}
else if (BIG_TUTO == $s['soumission_type_tuto'])
{
	$titre = $s['big_tuto_titre'];
	$voir = 'big';
} ?>

<tr<?php if ($s['soumission_prioritaire']) echo ' class="UI_errorrow"'; elseif ($s['soumission_recorrection'] && !empty($s['correction_date_fin'])) echo ' class="UI_inforow"'; ?>>
	<td class="centre">[SdZ] <?php echo $liste_types[$s['soumission_type_tuto']] ?></td>
	<td><a href="/zcorrection/fiche-tuto-<?php echo $s['soumission_id'] ?>.html">
		<?php echo htmlspecialchars($titre) ?>
	</a> (<?php echo round(@filesize(BASEPATH.'/web/tutos/'.$s['soumission_sauvegarde'])/1000, 2) ?> ko)</td>
	<td class="centre">
		<a href="http://www.siteduzero.com/membres-294-<?php echo $s['tutoteur_idsdz'] ?>.html">
			<?php echo htmlspecialchars($s['tutoteur_pseudo']) ?>
		</a><br />
		Soumis par 
		<a href="http://www.siteduzero.com/membres-294-<?php echo $s['valido_idsdz'] ?>.html">
			<?php echo htmlspecialchars($s['valido_pseudo']) ?>
		</a>
	</td>
	<td class="centre"><?php echo dateformat($s['soumission_date'], DATE) ?></td>
	
	<?php if ($type !== 'admin'): ?>
	<td><?php echo $description ?></td>
	<?php endif; ?>
	
	<?php if ($type === 'correcteur'): ?>
	<td class="centre">
		<?php if (empty($s['correction_date_debut'])): ?>
			Correction non commencée.
		<?php elseif (empty($s['correction_date_fin'])): ?>
			Correction commencée <?php echo dateformat($s['correction_date_debut'], MINUSCULE, DATE) ?>.
		<?php elseif (empty($s['recorrection_date_debut']) && $s['soumission_recorrection']): ?>
			Correction terminée <?php echo dateformat($s['correction_date_fin'], MINUSCULE, DATE) ?>.<br />
			Recorrection non commencée.
		<?php elseif (empty($s['recorrection_date_fin']) && $s['soumission_recorrection']): ?>
			Correction terminée <?php echo dateformat($s['correction_date_fin'], MINUSCULE, DATE) ?>.<br />
			Recorrection commencée <?php echo dateformat($s['recorrection_date_debut'], MINUSCULE, DATE) ?>.
		<?php else: ?>
			Correction et recorrection terminées.
		<?php endif; ?>
	</td>
	<td>
		<?php if (empty($s['correction_date_debut'])): ?>
			<a href="/zcorrection/corriger-<?php echo $s['soumission_id'] ?>.html"><img src="/img/zcorrection/corriger.png" title="Commencer la correction !" alt="Commencer la correction !" /></a>
		<?php elseif (empty($s['correction_date_fin'])): ?>
			<a href="/zcorrection/corriger-<?php echo $s['soumission_id'] ?>.html"><img src="/img/zcorrection/corriger.png" title="Continuer la correction" alt="Continuer la correction" /></a>
		<?php elseif (empty($s['recorrection_date_debut']) && $s['soumission_recorrection']): ?>
			<a href="/zcorrection/corriger-<?php echo $s['soumission_id'] ?>.html"><img src="/img/zcorrection/corriger.png" title="Commencer la recorrection !" alt="Commencer la recorrection !" /></a>
		<?php elseif (empty($s['recorrection_date_fin']) && $s['soumission_recorrection']): ?>
			<a href="/zcorrection/corriger-<?php echo $s['soumission_id'] ?>.html"><img src="/img/zcorrection/corriger.png" title="Continuer la recorrection !" alt="Continuer la recorrection !" /></a>
		<?php endif; ?>
		
		<a href="?abandon=<?php echo $s['soumission_id'] ?>"><img src="/img/zcorrection/abandonner.png" title="Abandonner" alt="Abandonner" /></a>
		
		<?php if (!empty($s['correction_date_debut']) AND empty($s['correction_date_fin'])): ?>
			<a href="?reprise_correction_zero=<?php echo $s['soumission_id'] ?>"><img src="/img/zcorrection/reprise_depuis_zero.png" alt="Recommencer la correction" title="Recommencer la correction" /></a>
		<?php elseif (!empty($s['recorrection_date_debut']) AND empty($s['recorrection_date_fin'])): ?>
			<a href="?reprise_correction_zero=<?php echo $s['soumission_id'] ?>"><img src="/img/zcorrection/reprise_depuis_zero.png" alt="Recommencer la recorrection" title="Recommencer la recorrection" /></a>
		<?php endif; ?>
	</td>
	<?php elseif ($type === 'admin'): ?>
	<?php $correction_id = !empty($s['recorrection_id']) ? $s['recorrection_id'] : $s['correction_id'] ?>
	<td>
		1<sup>ère</sup> : 
		<?php if (empty($s['id_correcteur'])): ?>
			-
		<?php else: ?>
			<a href="/membres/profil-<?php echo $s['id_correcteur'] ?>-<?php echo rewrite($s['pseudo_correcteur']) ?>.html">
				<?php echo htmlspecialchars($s['pseudo_correcteur']) ?>
			</a> - 
			<?php if ($s['correction_abandonee']): ?>
				Abandonnée
			<?php elseif (!empty($s['correction_date_debut'])): ?>
				<?php echo dateformat($s['correction_date_debut']) ?>
			<?php else: ?>
				Non débutée
			<?php endif; ?>
			<?php if (!empty($s['correction_date_fin'])): ?>
			 	→ <?php echo dateformat($s['correction_date_fin']) ?>
			<?php endif; ?>
		<?php endif; ?><br />
		
		2<sup>ème</sup> : 
		<?php if (empty($s['id_recorrecteur'])): ?>
			-
		<?php else: ?>
			<a href="/membres/profil-<?php echo $s['id_recorrecteur'] ?>-<?php echo rewrite($s['pseudo_recorrecteur']) ?>.html">
				<?php echo htmlspecialchars($s['pseudo_recorrecteur']) ?>
			</a> - 
			<?php if ($s['recorrection_abandonee']): ?>
				Abandonnée
			<?php elseif (!empty($s['recorrection_date_debut'])): ?>
				<?php echo dateformat($s['recorrection_date_debut']) ?>
			<?php else: ?>
				Non débutée
			<?php endif; ?>
			<?php if (!empty($s['recorrection_date_fin'])): ?>
			 	→ <?php echo dateformat($s['recorrection_date_fin']) ?>
			<?php endif; ?>
		<?php endif; ?>
	</td>
	<td class="centre" style="vertical-align: middle;">
		<?php if (verifier('zcorrection_retirer')): ?>
			<?php if (!$s['correction_abandonee'] && !empty($correction_id)): ?>
				<a href="?retirer=<?php echo $correction_id ?>">
					<img src="/img/zcorrection/abandonner.png" alt="Retirer la correction" title="Retirer la correction" />
				</a>
			<?php endif; ?>
		<?php endif; ?>

		<?php if (verifier('zcorrection_priorite')): ?>
			<?php if ($s['soumission_prioritaire']): ?>
				<a href="?nonprioritaire=<?php echo $s['soumission_id'] ?>">
					<img src="/img/zcorrection/nonprioritaire.png" title="Cette correction n'est plus prioritaire" alt="Plus prioritaire" />
				</a>
			<?php else: ?>
				<a href="?prioritaire=<?php echo $s['soumission_id'] ?>">
					<img src="/img/zcorrection/prioritaire.png" title="Cette correction est prioritaire" alt="Prioritaire" />
				</a>
			<?php endif; ?>
		<?php endif; ?>

		<?php if (verifier('zcorrection_editer_tutos')): ?>
			<?php if (!empty($correction_id)): ?>
				<a href="corriger-<?php echo $s['soumission_id'] ?>.html">
					<img src="/img/editer.png" alt="Éditer" />
				</a>
			<?php endif; ?>
		<?php endif; ?>
	</td>
	<?php else: ?>
	<td>
		<?php if ($s['soumission_recorrection']): ?>
			<?php if (empty($s['recorrection_id'])): ?>
				Première correction par <a href="/membres/profil-<?php echo $s['id_correcteur'] ?>-<?php echo rewrite($s['pseudo_correcteur']) ?>.html"><?php echo htmlspecialchars($s['pseudo_correcteur']) ?></a>.
			<?php elseif(!empty($s['recorrection_abandonee'])): ?>
				Première correction par <a href="/membres/profil-<?php echo $s['id_correcteur'] ?>-<?php echo rewrite($s['pseudo_correcteur']) ?>.html"><?php echo htmlspecialchars($s['pseudo_correcteur']) ?></a><br />
				Abandonnée par <a href="/membres/profil-<?php echo $s['id_recorrecteur'] ?>-<?php echo rewrite($s['pseudo_recorrecteur']) ?>.html"><?php echo htmlspecialchars($s['pseudo_recorrecteur']) ?></a>.
			<?php endif; ?>
		<?php elseif (empty($s['correction_id'])): ?>
			Non pris en charge.
		<?php elseif (!empty($s['correction_abandonee'])): ?>
			Abandonnée par <a href="/membres/profil-<?php echo $s['id_correcteur'] ?>-<?php echo rewrite($s['pseudo_correcteur']) ?>.html"><?php echo htmlspecialchars($s['pseudo_correcteur']) ?></a>.
		<?php endif; ?>
	</td>
	<td class="centre">
		<?php if (!empty($s['correction_id_tuto_corrige_1'])): ?>
		<a href="voir-<?php echo $voir ?>-tuto-<?php echo $s['correction_id_tuto_corrige_1'] ?>.html?cid=<?php echo $s['soumission_id'] ?>">
			<img src="/img/zcorrection/voir.png" alt="Voir" title="Voir" />
		</a>
		<?php else: ?>
		<a href="voir-<?php echo $voir ?>-tuto-<?php echo $s['soumission_id_tuto'] ?>.html?cid=<?php echo $s['soumission_id'] ?>">
			<img src="/img/zcorrection/voir.png" alt="Voir" title="Voir" />
		</a>
		<?php endif; ?>

		<?php if (verifier('zcorriger')): ?>
			<a href="index-<?php echo $s['soumission_id'] ?>.html">
				<img src="/img/zcorrection/prendre.png" alt="Prendre en charge" title="Je m'en charge !" />
			</a>
		<?php endif; ?>
	</td>
	<?php endif; ?>
</tr>