<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Bannir une plage d'adresses mails</h1>

<p>
	Les adresses mails bannies ne pourront pas être utilisées à l'inscription
	ou lors d'un changement d'adresse. Le bannissement fonctionne par domaine entier.
	Vous devez spécifier soit un domaine et un TLD, par exemple <em>zcorrecteurs.fr</em>,
	soit utiliser un joker, par exemple <em>zcorrecteurs.*</em> qui bannira
	<em>zcorrecteurs.fr</em>, <em>zcorrecteurs.net</em>, <em>zcorrecteurs.org</em>, etc.
</p>

<form method="post" action="<?php echo $view['router']->generate('zco_user_admin_newBannedEmail') ?>" class="form-horizontal">
	<?php echo $view['form']->errors($form) ?>
	<?php echo $view['form']->row($form['email']) ?>
	<?php echo $view['form']->row($form['reason']) ?>
	<?php echo $view['form']->rest($form) ?>

	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Envoyer" />
	</div>
</form>