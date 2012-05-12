<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Répondre à une demande</h1>

<p>
	Vous vous apprêtez à répondre à la demande de <strong><a href="<?php echo $view['router']->generate('zco_user_profile', array('id' => $query->getUserId(), 'slug' => rewrite($query->getUser()->getUsername()))) ?>"><?php echo htmlspecialchars($query->getUser()) ?></a></strong>.
	Il désire changer son pseudo pour <strong><?php echo htmlspecialchars($query->getNewUsername()) ?></strong>.
</p>
	
<p>
	<span class="citation">Citation : <?php echo htmlspecialchars($query->getUser()) ?></span>
	<div class="citation2"><?php echo $view['messages']->parse($query->getReason()) ?></div>
</p>

<form method="post" action="<?php echo $view['router']->generate('zco_user_admin_newPseudoAnswer', array('id' => $query->getId())) ?>" class="form-horizontal">
	<?php echo $view['form']->errors($form) ?>
	<?php echo $view['form']->row($form['status']) ?>
	<?php echo $view['form']->row($form['adminResponse']) ?>
	<?php echo $view['form']->rest($form) ?>
	
	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Envoyer" />
	</div>
</form>
