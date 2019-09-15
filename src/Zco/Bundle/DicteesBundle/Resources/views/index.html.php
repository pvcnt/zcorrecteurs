<?php $view->extend('::layouts/default.html.php') ?>

<h1>Dictées</h1>

<p class="gras centre">
	<a href="<?php echo $view['router']->path('zco_dictation_list') ?>">Liste complète des dictées</a>
    <?php if (verifier('connecte')): ?>
        - <a href="<?php echo $view['router']->path('zco_dictation_myStats') ?>">Mes statistiques de réussite</a>
    <?php endif ?>
</p>

<table class="UI_boxes" cellspacing="7px">
	<tr><td>
		<h2 class="mod_une">Les plus récentes</h2>
		<dl>
		<?php if(!count($DicteesAccueil)): ?>
			<dd><em>Aucune dictée trouvée.</em></dd>
		<?php else: foreach($DicteesAccueil as $dictee): ?>
			<dd>
			<div <?php if(!$dictee->icone) echo 'style="height:45px; display: table-cell; vertical-align:middle;"' ?>>
			<?php if ($dictee->icone) :?>
				<img src="<?php echo htmlspecialchars($dictee->icone); ?>" height="40" width="40" style="float:left;"/>
			<?php endif; ?>
				<p <?php if($dictee->icone) echo 'style="text-indent:5px;"'; else echo 'style="text-indent:40px;"' ?>>
				<a href="dictee-<?php echo $dictee->id ?>-<?php
				   echo rewrite($dictee->titre) ?>.html"
				   title="Difficulté : <?php echo $DicteeDifficultes[$dictee->difficulte] ?>" style="text-indent:5px;">
					 <?php echo htmlspecialchars($dictee->titre) ?>
				</a>
				<?php if($dictee->description): ?>
					<div class="dictee-description" <?php if($dictee->icone) echo 'style="text-indent:20px;"'; else echo 'style="text-indent:55px;"' ?> >
						<?php echo extrait(strip_tags($dictee->description),70) ?>
					</div></p>
				<?php else : ?>
					<div class="dictee-description">
						<br/>
					</div></p>
				<?php endif ?>
			</div>
			</dd>
		<?php endforeach; endif ?>
		</dl>
	</td>
	<td>
		<h2 class="mod_communaute">Les plus jouées</h2>
		<dl>
		<?php if(!count($DicteesLesPlusJouees)): ?>
			<dd><em>Aucune dictée trouvée.</em></dd>
		<?php else: foreach($DicteesLesPlusJouees as $dictee): ?>
			<dd>
			<div <?php if(!$dictee->icone) echo 'style="height:45px; display: table-cell; vertical-align:middle;"' ?>>
			<?php if ($dictee->icone) :?>
				<img src="<?php echo htmlspecialchars($dictee->icone); ?>" height="40" width="40" style="float:left;"/>
			<?php endif; ?>
			<p <?php if($dictee->icone) echo 'style="text-indent:5px;"'; else echo 'style="text-indent:40px;"' ?>>
				<a href="dictee-<?php echo $dictee->id ?>-<?php
				   echo rewrite($dictee->titre) ?>.html"
				   title="Difficulté : <?php echo $DicteeDifficultes[$dictee->difficulte] ?>" >
					<?php echo htmlspecialchars($dictee->titre) ?>
				</a>
				<?php if($dictee->description): ?>
					<div class="dictee-description" <?php if($dictee->icone) echo 'style="text-indent:20px;"'; else echo 'style="text-indent:55px;"' ?> >
						<?php echo extrait(strip_tags($dictee->description),70) ?>
					</div></p>
				<?php else : ?>
					<div class="dictee-description">
						<br/>
					</div></p>
				<?php endif ?>
				</div>
			</dd>
		<?php endforeach; endif ?>
		</dl>
	</td></tr>

	<tr><td>
		<h2 class="mod_quiz">Une dictée au hasard</h2>
		<dl>
		<?php if(!$DicteeHasard): ?>
			<dd><em>Aucune dictée trouvée.</em></dd>
		<?php else: $dictee = $DicteeHasard ?>
			<dd>
			<?php if ($dictee->icone) :?>
				<img src="<?php echo htmlspecialchars($dictee->icone); ?>" height="100" width="100" style="float:left;"/>
			<?php endif; ?>
			<div <?php if ($dictee->icone) echo 'style="text-indent:5px;"' ?>>
				<a href="dictee-<?php echo $dictee->id ?>-<?php
				   echo rewrite($dictee->titre) ?>.html"
				   title="Difficulté : <?php echo $DicteeDifficultes[$dictee->difficulte] ?>">
					<?php echo htmlspecialchars($dictee->titre) ?>
				</a>
			</div>
			</dd>
			<dd class="dictee-description">
				<div <?php if ($dictee->icone) echo 'style="margin-left:120px;"'?>><?php echo $view['messages']->parse($dictee->description, array(
				    'core.anchor_prefix' => $dictee['id'],
				    'files.entity_id' => $dictee['id'],
				    'files.entity_class' => 'Dictee',
					'files.part' => 1,
				)) ?></div>
			</dd>
		<?php endif ?>
		</dl>
	</td>
	<td>
		<h2 class="mod_sondage">Statistiques</h2>
		<ul class="forum_stats">
			<li>Nombre de dictées : <?php echo $Statistiques->nombreDictees ?></li>
			<li>Nombre de participations : <?php echo $Statistiques->nombreParticipations ?></li>
		</ul>
	</tr>
</table>