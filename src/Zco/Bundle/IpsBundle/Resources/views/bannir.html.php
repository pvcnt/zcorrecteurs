<?php $view->extend('::layouts/default.html.php') ?>

<h1>Bannir une adresse IP</h1>

<p>Cette page vous permet de bannir des adresses IPs. Cela signifie que tout membre utilisant cette adresse sera
automatiquement redirigé vers une page de bannissement.<br />
<strong>Cette option est à utiliser avec précaution.</strong></p>

<fieldset>
	<legend>Bannir une nouvelle adresse IP</legend>
	<form method="post" action="">
		<div class="send">
			<input type="submit" value="Envoyer" accesskey="s" />
		</div>

		<label for="ip">Adresse IP : </label>
		<input type="text" name="ip" id="ip" value="<?php echo isset($_GET['ip']) ? $_GET['ip'] : ''; ?>" /><br />
		<label for="duree">Durée en jours : </label>
		<input type="text" size="2" id="duree" name="duree" value="3" /> <em>(0 pour toujours)</em><br />

		<label for="raison">Raison visible par le membre : </label>
		<?php echo $view->render('::zform.html.php', array('id' => 'raison')); ?><br />
		<label for="texte">Raison visible par les admins : </label><br />
		<?php echo $view->render('::zform.html.php', array('id' => 'texte')); ?><br />

		<div class="send">
			<input type="submit" value="Envoyer" accesskey="s" />
		</div>
	</form>
</fieldset>
