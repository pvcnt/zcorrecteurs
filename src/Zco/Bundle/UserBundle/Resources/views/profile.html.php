<?php $view->extend('::layouts/bootstrap.html.php') ?>

<div style="border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; margin-bottom: 5px; border: 1px solid #EEE; background-color: whiteSmoke; padding: 10px;">
	<div class="row-fluid">
		<div class="span2" style="text-align: center;">
			<div class="avatar" style="margin-bottom: 5px;">
				<img src="<?php echo $user->getAvatar() ?>" />
			</div>
			<?php echo $view->get('messages')->userGroup($user) ?>
		</div>
		<div class="span10">
			<h1 style="margin-top: 0;">
				Profil d<?php echo $art.htmlspecialchars($user->getUsername()) ?>
				<?php if ($user->hasCitation()): ?>
					<small><?php echo htmlspecialchars($user->getCitation()) ?></small>
				<?php endif ?>
			</h1>

			<?php echo $view->render('ZcoUserBundle:Profile:_profile.html.php', compact('user')) ?>
		</div> <!-- /.span10 -->
	</div> <!-- /.row-fluid -->
</div>

<div class="row-fluid" style="margin-top: 20px;">
	<div class="span9" style="padding-right: 10px; border-right: 1px solid #EEE;">
		<div class="pill-content">
			<div class="pill-pane active" id="profile-profile">
				<?php if ($user->hasBiography()): ?>
					<?php echo $view['messages']->parse($user->getBiography(), array('core.anchor_prefix' => 'bio')) ?>
				<?php else: ?>
					<div class="alert alert-info">
						<?php echo htmlspecialchars($user->getUsername()) ?> n’a pas encore écrit sa biographie.
					</div>
				<?php endif ?>

				<?php if ($user->hasSignature()): ?>
					<hr style="margin-bottom: 10px;" />
					<div style="background-color: #FCF8E3; padding: 10px; border-radius: 5px;">
						<?php echo $view['messages']->parse($user->getSignature()) ?>
					</div>
				<?php endif ?>
			</div>
			<?php if ($canSeeInfos): ?>
			<div class="pill-pane" id="profile-infos">
				<?php echo $view->render('ZcoUserBundle:Profile:_infos.html.php', compact('user', 'newPseudo', 'warnings', 'punishments', 'ListerGroupes', 'ListerIPs')) ?>
			</div>
			<?php endif ?>
		</div> <!-- /.pill-content -->
	</div> <!-- /.span9 -->

	<div class="span3">
		<?php if ($canSeeInfos) :?>
		<ul class="nav nav-pills nav-stacked" style="margin-bottom: 15px;">
		    <li class="active"><a href="#profile-profile" data-toggle="pill">Profil</a></li>
		    <?php if ($canSeeInfos): ?>
		  	<li><a href="#profile-infos" data-toggle="pill">Informations</a></li>
		  	<?php endif ?>
		</ul>
		<?php endif ?>

		<?php if ($user->getId() == $_SESSION['id']): ?>
        <div style="margin-top: 5px;">
			<a class="btn btn-success" 
			   href="<?php echo $view['router']->generate('zco_options_index') ?>" 
			   style="display: inline-block; width: 80%;"
			>
				<i class="icon-cog icon-white"></i> 
				Modifier mes options
			</a>
		</div>
		<?php else: ?>
		<div>
			<a class="btn btn-success<?php if (!$canSendEmail): ?> disabled<?php endif ?>" 
				style="display: inline-block; width: 80%;"
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
				style="display: inline-block; width: 80%;"
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
            <a class="btn dropdown-toggle" style="display: inline-block; width: 80%;" data-toggle="dropdown" href="#">
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
	            <?php if(verifier('recrutements_voir_candidatures')): ?>
	            	<li>
	            		<a href="/recrutement/candidatures-membre-<?php echo $user->getId(); ?>.html">
	            			<?php echo $_SESSION['id'] == $user->getId() ? 'Mes' : 'Ses' ?> 
	            			candidatures aux recrutements
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
	        	<?php if(verifier('quiz_stats')|| $_SESSION['id'] == $user->getId()): ?>
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
            <a class="btn dropdown-toggle" style="display: inline-block; width: 80%;" data-toggle="dropdown" href="#">
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
	</div> <!-- /.span3 -->
</div> <!-- /.row-fluid -->