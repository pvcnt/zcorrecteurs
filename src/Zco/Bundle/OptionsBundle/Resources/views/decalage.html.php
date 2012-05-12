<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modification du fuseau horaire</h1>

<form method="post" action="">
	<fieldset>
		<legend>Décalage horaire</legend>

		<p style="padding-left: 40px">
		<?php foreach ($decalages as $decalage => $nom): ?>
			<input type="radio"
			       name="decalage"
			       id="decal<?php echo $decalage ?>"
			       value="<?php echo $decalage ?>"
			       <?php if ($pref == $decalage) echo ' checked="checked"' ?>
			/>
			<label class="nofloat" for="decal<?php echo $decalage ?>">
				<?php echo $nom ?>
			</label>
			<br/>
		<?php endforeach ?>
		</p>
		<p class="centre">
			<input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>"/>
			<input type="submit" value="Modifier →"/>
		</p>
	</fieldset>
</form>
