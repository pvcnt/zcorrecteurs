<div class="row-fluid">
	<div class="span6">
		<ul>
			<?php if (verifier('groupes_changer_membre')): ?>
			<li>
				<a href="/groupes/changer-membre-groupe-<?php echo $user->getId() ?>.html">
					Changer de groupe
				</a>
			</li>
			<?php endif ?>
			<?php if (verifier('options_editer_profils')): ?>
			<li>
				<a href="/options/modifier-mot-de-passe-<?php echo $user->getId() ?>.html">
					Modifier ses paramètres
				</a>
			</li>
			<?php endif ?>
			<?php if (verifier('membres_editer_titre')): ?>
		    <li>
		    	<a href="<?php echo $view['router']->generate('zco_user_editTitle', array('id' => $user->getId())) ?>">
		    		Modifier son titre
		    	</a>
		    </li>
			<?php endif ?>
		</ul>
	</div>
</div>