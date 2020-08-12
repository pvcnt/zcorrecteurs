<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoAboutBundle::tabs.html.php', array('currentTab' => 'corrigraphie')) ?>

<div class="row">
	<div class="span8">
		<p style="text-align: justify;">
			Début 2011, soit quatre ans après la création du site, fut prise la décision de fonder 
			une association. Cette association, dénommée Corrigraphie,
			a depuis pour objectif de donner une existence légale au site, de favoriser 
			l’implication de ses membres et de réfléchir à des façons de développer le 
			site et de renforcer sa présence. Vaste programme !
		</p>

		<p style="text-align: justify; margin-top: 15px;">
			Concrètement, l’association regroupe des membres qui souhaitent <strong>s’investir plus en 
			avant dans la vie et la gestion du site</strong>. L’association gère des choses différentes 
			de l’équipe du site, avec qui elle travaille main dans la main. Ainsi, elle s’occupe essentiellement 
			des aspects administratifs et financiers pour laisser l’équipe se concentrer sur l’animation 
			du site au jour le jour. Elle est aussi l’entité qui représente officiellement 
			le site ; à ce titre elle est amenée à communiquer pour mieux faire connaître le site et permettre 
			à toujours plus de personnes de bénéficier de ses services.
		</p>
	</div>

	<div class="span4">
        <img src="/img/corrigraphie.png" />
	</div>
</div>

<div class="row">
	<div class="span6 well">
		<h2>Devenir bénévole</h2>
	
		<p style="text-align: justify;">
			Si vous êtes intéressés par le travail de l’association, plusieurs moyens 
			sont à votre disposition pour vous tenir au courant et participer.
		</p>
	
		<p style="margin-top: 15px; text-align: justify;">
			La façon la plus simple de contribuer à notre projet est de prendre part 
			aux discussions sur le <a href="/forum/forum-178-l-association-corrigraphie.html">forum de l’association</a> 
			et d’y proposer des idées. Vous y serez également informés de nos futures 
			manifestations.
		</p>
	
		<p style="margin-top: 15px; text-align: justify;">
			Une autre façon est bien sûr de rejoindre 
			<a href="<?php echo $view['router']->generate('zco_about_team') ?>">notre équipe</a>
			et de vous investir activement dans le fonctionnement quotidien du site. Il 
			s’agit d’une expérience très enrichissante et formatrice au sein d’une équipe 
			dynamique. Pour en savoir plus, n’hésitez pas à consulter notre 
			<a href="/recrutement/">espace dédié aux recrutements</a>.
	
		<p style="margin-top: 15px; text-align: justify;">
			Nous privilégions une intégration progressive des membres dans l’association. 
			Cela signifie que toute personne souhaitant nous aider est invitée à commencer 
			par donner de son temps, à la mesure de ses possibilités, en tant que bénévole.
			Cela peut ensuite mener à une adhésion si la personne le souhaite !
		</p>
	</div>

	<div class="span5 well">
		<h2>Faire un don</h2>
		<?php echo $this->render('ZcoDonsBundle::_formulaireDon.html.php', array('chequeOuVirement' => false)) ?>
	</div>
</div>