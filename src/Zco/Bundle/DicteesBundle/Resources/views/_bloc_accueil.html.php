<p class="centre italique"><a href="/dictees/">Accéder aux dictées</a></p>

<ul>
	<li>
		Dictées les plus fréquentées
	
		<ul class="lightning">
			<?php if(!count($DicteesLesPlusJouees)): ?>
				<li><em>Aucune dictée trouvée.</em></li>
			<?php else: foreach($DicteesLesPlusJouees as $dictee): ?>
				<li>
					<a href="/dictees/dictee-<?php echo $dictee->id ?>-<?php
					   echo rewrite($dictee->titre) ?>.html">
						<?php echo htmlspecialchars($dictee->titre) ?>
					</a>
					<?php if($dictee->description): ?>
						<span class="dictee-description">
							—
							<?php echo extrait(strip_tags($dictee->description)) ?>
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
					<a href="/dictees/dictee-<?php echo $dictee->id ?>-<?php
					   echo rewrite($dictee->titre) ?>.html">
						<?php echo htmlspecialchars($dictee->titre) ?>
					</a>
					<?php if($dictee->description): ?>
						<span class="dictee-description">
							—
							<?php echo extrait(strip_tags($dictee->description)) ?>
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
					<a href="/dictees/dictee-<?php echo $DicteeHasard->id ?>-<?php
					   echo rewrite($DicteeHasard->titre) ?>.html">
						<?php echo htmlspecialchars($DicteeHasard->titre) ?>
					</a>
                    <?php if($dictee->description): ?>
                        <span class="dictee-description">
							—
                            <?php echo extrait(strip_tags($dictee->description)) ?>
						</span>
                    <?php endif ?>
				</li>
			<?php endif ?>
		</ul>
	</li>
</ul>
