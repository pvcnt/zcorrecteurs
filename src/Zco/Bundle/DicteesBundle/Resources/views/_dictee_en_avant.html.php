<p class="centre italique"><a href="/dictees/">Accéder aux dictées</a></p>
<a href="/dictees/dictee-<?php echo $Dictee->id ?>-<?php echo rewrite($Dictee->titre) ?>.html"><h3><?php echo $Dictee->titre; ?></h3></a>
<br/>

<a href="/dictees/dictee-<?php echo $Dictee->id ?>-<?php echo rewrite($Dictee->titre) ?>.html">
	<img src="<?php echo $Dictee->icone; ?>" height="100" width="100" style="float :left; margin-left: 30px;" />
</a>	
<dl style="margin-left: 130px;">
	<dd title="Difficulté : <?php echo $Dictee->difficulte; ?>">
		<ul class="star-rating" style="width: 120px">
			<li class="current-rating" style="width: <?php echo $Dictee->difficulte * 30 ?>px"></li>
		</ul>
	</dd>
	<?php if($Dictee->source): ?>
		<dd><strong>Source :</strong> <?php echo htmlspecialchars($Dictee['source']) ?></dd>
	<?php endif;?>
	<?php if(!empty($Dictee->Auteur->nom)): ?>
		<dd><strong>Auteur :</strong>
		    <a href="/auteurs/auteur-<?php echo $Dictee->Auteur->id.'-'.rewrite($Dictee->Auteur) ?>.html">
		    <?php echo htmlspecialchars($Dictee->Auteur) ?></a>
		</dd>
	<?php endif ?>
	<dd>
		<strong>Description :</strong>
		<span class="dictee-description"><?php echo extrait(strip_tags($Dictee->description)); ?></span>
	</dd>
</dl>
<br/>