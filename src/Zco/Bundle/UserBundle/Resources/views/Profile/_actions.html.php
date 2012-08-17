<?php if ($own): ?>
<div style="margin-top: 5px;">
	<a class="btn btn-success" 
	   href="<?php echo $view['router']->generate('zco_options_index') ?>" 
	   style="display: inline-block; width: 196px;"
	>
		<i class="icon-cog icon-white"></i> 
		Modifier mes options
	</a>
</div>
<?php else: ?>
<div>
	<a class="btn btn-success<?php if (!$canSendEmail): ?> disabled<?php endif ?>" 
		style="display: inline-block; width: 196px;"
	    <?php if ($canSendEmail): ?>
	    href="mailto:<?php echo $view['humanize']->email($user->getEmail()) ?>"
		<?php else: ?>
		href="#" onclick="return false;"
		<?php endif ?>
	>
		<i class="icon-envelope icon-white"></i> 
		Envoyer un courriel
	</a>
</div>
<div style="margin-top: 5px;">
	<a class="btn btn-primary<?php if (!$canSendMp): ?> disabled<?php endif ?>" 
		style="display: inline-block; width: 196px;"
	    <?php if ($canSendMp): ?>
	    href="/mp/nouveau-<?php echo $user->getId(); ?>.html"
		<?php else: ?>
		href="#" onclick="return false;"
		<?php endif ?>
	>
		<i class="icon-pencil icon-white"></i> 
		Envoyer un message privé
	</a>
</div>
<?php endif ?>
<div style="margin-top: 5px;"><div class="btn-group">
    <a class="btn dropdown-toggle" style="display: inline-block; width: 200px;" data-toggle="dropdown" href="#">
    	<i class="icon-comment"></i>
    	<?php echo $own ? 'Mon' : 'Son' ?> activité sur le site
    	<span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
    	<li>
    		<a href="/forum/detail-messages-<?php echo $user->getId(); ?>.html">
    			Détail de <?php echo $own ? 'mon' : 'son' ?> activité sur le forum
    		</a>
    	</li>
        <li>
        	<a href="/forum/messages-<?php echo $user->getId() ?>-<?php echo rewrite($user->getUsername()) ?>.html">
        		<?php echo $own ? 'Mes' : 'Ses' ?> messages sur le forum
        	</a>
        </li>
        <li>
        	<a href="/forum/sujets-participe-<?php echo $user->getId(); ?>.html">
        		Les sujets auxquels <?php echo $own ? 'j\'ai' : 'il a' ?> participé
        	</a>
        </li>
        <li class="divider"></li>
        <li>
        	<a href="/blog/billets-rediges-<?php echo $user->getId(); ?>.html">
        		<?php echo $own ? 'Mes' : 'Ses' ?> billets sur le blog
        	</a>
        </li>
        <?php if (verifier('recrutements_voir_candidatures')): ?>
        	<li>
        		<a href="/recrutement/candidatures-membre-<?php echo $user->getId(); ?>.html">
        			<?php echo $own ? 'Mes' : 'Ses' ?> candidatures aux recrutements
        		</a>
        	</li>
        <?php endif ?>
        <?php if (verifier('stats_zcorrecteurs')): ?>
            <li>
            	<a href="/statistiques/zcorrecteur-<?php echo $user->getId(); ?>.html">
            		<?php echo $own ? 'Mes' : 'Ses' ?> statistiques de zCorrection
            	</a>
            </li>
    	<?php endif ?>
    	<?php if (verifier('quiz_stats') || $own): ?>
        	<li>
        		<a href="/quiz/mes-statistiques-<?php echo $user->getId(); ?>.html">
        			<?php echo $own ? 'Mes' : 'Ses' ?> statistiques de quiz
        		</a>
        	</li>
    	<?php endif ?>
    </ul>
</div></div>
<?php if ($canAdmin): ?>
<div style="margin-top: 5px;"><div class="btn-group">
    <a class="btn dropdown-toggle" style="display: inline-block; width: 200px;" data-toggle="dropdown" href="#">
    	<i class="icon-wrench"></i>
    	Administrer le compte
    	<span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
		<?php if (verifier('groupes_changer_membre')): ?>
		<li>
			<a href="/groupes/changer-membre-groupe-<?php echo $user->getId() ?>.html">
				Changer de groupe
			</a>
		</li>
		<?php endif ?>
		<?php if (verifier('options_editer_profils')): ?>
		<li>
			<a href="<?php echo $view['router']->generate('zco_options_profile', array('id' => $user->getId())) ?>">
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
</div></div>
<?php endif ?>