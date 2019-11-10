<p class="centre italique"><a href="<?php echo $view['router']->path('zco_dictation_index') ?>">Accéder aux dictées</a></p>

<ul>
	<li>
		Dictées les plus fréquentées
	
		<ul class="lightning">
			<?php if(!count($DicteesLesPlusJouees)): ?>
				<li><em>Aucune dictée trouvée.</em></li>
			<?php else: foreach($DicteesLesPlusJouees as $dictee): ?>
				<li>
                    <a href="<?php echo $view['router']->path('zco_dictation_show', ['id' => $dictee->id, 'slug' => rewrite($dictee->titre)]) ?>">
						<?php echo htmlspecialchars($dictee->titre) ?>
					</a>
					<?php if($dictee->description): ?>
						<span class="dictee-description">
							—
							<?php echo $view['humanize']->summarize($dictee->description) ?>
						</span>
					<?php endif ?>
				</li>
			<?php endforeach; endif ?>
		</ul>
	</li>
	
	<li>
		Nouvelles dictées
		
		<ul class="add">
			<?php if(!count($DicteesAccueil)): ?>
				<li><em>Aucune dictée trouvée.</em></li>
			<?php else: foreach($DicteesAccueil as $dictee): ?>
				<li>
                    <a href="<?php echo $view['router']->path('zco_dictation_show', ['id' => $dictee->id, 'slug' => rewrite($dictee->titre)]) ?>">
					<?php if($dictee->description): ?>
						<span class="dictee-description">
							—
							<?php echo $view['humanize']->summarize($dictee->description) ?>
						</span>
					<?php endif ?>
				</li>
			<?php endforeach; endif ?>
		</ul>
	</li>
	
	<li>
		Une dictée au hasard
		
		<ul class="wand">
			<?php if(!$DicteeHasard): ?>
				<li><em>Aucune dictée trouvée.</em></li>
			<?php else: ?>
				<li>
                    <a href="<?php echo $view['router']->path('zco_dictation_show', ['id' => $dictee->id, 'slug' => rewrite($dictee->titre)]) ?>">
                    <?php if($dictee->description): ?>
                        <span class="dictee-description">
							—
                            <?php echo $view['humanize']->summarize($dictee->description) ?>
						</span>
                    <?php endif ?>
				</li>
			<?php endif ?>
		</ul>
	</li>
</ul>
