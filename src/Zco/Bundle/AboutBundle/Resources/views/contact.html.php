<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoAboutBundle::tabs.html.php', array('currentTab' => 'contact')) ?>

<p class="good">
	Si vous avez besoin de joindre <a href="<?php echo $view['router']->generate('zco_about_team') ?>">l’équipe du site</a>
	de manière personnelle, nous vous invitons à utiliser le formulaire 
	ci-dessous et y formuler librement votre demande. Merci de sélectionner 
	la raison la plus appropriée afin d’accélérer le traitement de votre requête.
</p>

<form method="post" action="" class="form-horizontal">
	<fieldset>
		<legend>Demande de contact</legend>
		<?php echo $view['form']->row($form['raison']) ?>
		<div id="zco_apropos_contact_avertissement_partenariat" class="alert alert-block">
			<p class="good">Vous vous apprêtez à nous contacter pour un partenariat.
			S’il s’agit d'un échange de lien, ne proposez votre site
			<span class="gras rouge">que</span> si celui-ci a un <span class="italique">
			Page Rank</span> égal ou supérieur à 4. En effet, les partenariats 
			au niveau des liens ont été mis en place avec deux objectifs. Soit 
			apporter quelques fonds au site (vente de lien), soit aider pour le 
			référencement. Donc si votre site n’a pas de <span class="italique">Page Rank</span> 
			ou a un <span class="italique">Page Rank</span> très faible, nous 
			serons contraints de le refuser.</p>

			<p class="good">Nous espérons que vous comprendrez les raisons qui nous poussent à
			effectuer une telle sélection.</p>
		</div>

		<?php echo $view['form']->row($form['sujet']) ?>
		<?php echo $view['form']->row($form['nom']) ?>

		<?php if (verifier('connecte')): ?>
		<div class="control-group">
			<label for="pseudo" class="control-label">Pseudo sur le site *</label>
			<div class="controls">
				<input type="text" disabled="disabled" value="<?php echo htmlspecialchars($_SESSION['pseudo']) ?>" />
			</div>
		</div>
		<?php endif; ?>

		<?php echo $view['form']->row($form['courriel']) ?>
		<?php echo $view['form']->row($form['message']) ?>

		<?php echo $view['form']->rest($form) ?>

		<div class="form-actions">
			<input type="submit" value="Envoyer" class="btn" />
		</div>
	</fieldset>
</form>

<script type="text/javascript">
	<?php if (!isset($_GET['objet']) || $_GET['objet'] !== 'Partenariat'): ?>
	window.addEvent('domready', function()
	{
		$('zco_apropos_contact_avertissement_partenariat').slide('hide');
	});
	<?php endif; ?>
	$('zco_apropos_contact_raison').addEvent('change', function()
	{
		if ($('zco_apropos_contact_raison').get('value') == 'Partenariat')
		{
			$('zco_apropos_contact_avertissement_partenariat').slide('in');
		}
		else
		{
			$('zco_apropos_contact_avertissement_partenariat').slide('out');
		}
	});
</script>
