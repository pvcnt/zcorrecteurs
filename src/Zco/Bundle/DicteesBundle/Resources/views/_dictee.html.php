<div style="float: right; margin-left: 10px;">
<dl class="dictee-outils">

    <?php if ($Dictee->icone): ?>
	<div align="center">
	    <img src="<?php echo htmlspecialchars($Dictee->icone) ?>" height="100" width="100" style="float :center;"/>
	</div><br />
	<?php endif; ?>
	<dd title="Difficulté : <?php echo $DicteeDifficultes[$Dictee->difficulte] ?>">
		<ul class="star-rating star-centre" style="width: 120px">
		<li class="current-rating" style="width: <?php echo $Dictee->difficulte * 30 ?>px"></li></dd>
		</ul>
	</dd>
	<dd><strong>Participations :</strong> <?php echo $Dictee->participations ?></dd>
	<dd><strong>Créée :</strong> <?php echo dateformat($Dictee->creation, MAJUSCULE, DATE) ?></dd>
	<dd>
		<strong>Proposée par :</strong>
		<a href="/membres/profil-<?php echo $Dictee->Utilisateur->id ?>-<?php
			echo rewrite($Dictee->Utilisateur->pseudo) ?>.html">
			<?php echo $Dictee->Utilisateur->pseudo ?>
		</a>
	</dd>
	<?php if($Dictee->source): ?>
		<dd><strong>Source :</strong> <?php echo htmlspecialchars($Dictee->source) ?></dd>
	<?php endif ?>
	<?php if($Dictee->Auteur): ?>
		<dd><strong>Auteur :</strong>
		    <a href="/auteurs/auteur-<?php echo $Dictee->Auteur->id.'-'.rewrite($Dictee->Auteur) ?>.html">
		    <?php echo htmlspecialchars($Dictee->Auteur) ?></a>
		</dd>
	<?php endif ?>

	<?php if(DicteeDroit($Dictee, 'editer')): ?>
		<dd><a href="editer-<?php echo $Dictee->id ?>.html">
			<img title="Éditer" alt="Éditer" class="fff pencil" src="/pix.gif"/>
			Modifier</a></dd>
	<?php endif; if(DicteeDroit($Dictee, 'supprimer')): ?>
		<dd><a href="supprimer-<?php echo $Dictee->id ?>.html">
			<img title="Supprimer" alt="Supprimer" class="fff cross" src="/pix.gif"/>
			Supprimer</a></dd>
	<?php endif; if($Dictee->etat == DICTEE_VALIDEE && verifier('dictees_publier')): ?>
		<dd><a href="valider-<?php echo $Dictee->id ?>-0.html?token=<?php echo $_SESSION['token'] ?>">
			<img title="Dévalider" alt="Dévalider" class="fff forbidden" src="/pix.gif"/>
			Passer hors-ligne</a></dd>
	<?php endif; if($Dictee->etat != DICTEE_VALIDEE && verifier('dictees_publier')): ?>
		<dd><a href="valider-<?php echo $Dictee->id ?>-1.html?token=<?php echo $_SESSION['token'] ?>">
			<img title="Valider" alt="Valider" class="fff tick" src="/pix.gif"/>
			Passer en ligne</a></dd>
	<?php endif; if($Dictee->etat != DICTEE_VALIDEE): ?>
		<dd><strong>État :</strong>
			<?php echo $DicteeEtats[$Dictee->etat] ?>
		</dd>
	<?php endif ?>
</dl>
<br/>
<?php if (isset($extra)) echo $extra ?>
</div>


<?php if($Dictee->description): ?>
<h2>Description</h2>
<p><?php echo $view['messages']->parse($Dictee->description, array(
    'core.anchor_prefix' => $Dictee['id'],
    'files.entity_id' => $Dictee['id'],
    'files.entity_class' => 'Dictee',
	'files.part' => 1,
)) ?></p>
<?php endif ?>

<p class="italique"><a href="https://twitter.com/share?text=<?php echo urlencode('Vous aussi, mesurez-vous à cette dictée des @zCorrecteurs : ') ?>&url=<?php echo URL_SITE ?>/dictees/dictee-<?php echo $Dictee->id ?>-<?php echo rewrite($Dictee->titre) ?>.html">
    <img src="/bundles/zcotwitter/img/oiseau_16px.png" alt="Twitter" />
    Partager cette dictée sur Twitter
</a></p>
