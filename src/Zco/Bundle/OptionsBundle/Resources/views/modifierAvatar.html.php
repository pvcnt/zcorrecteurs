<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modifier l'avatar</h1>

<p>
	Un avatar est une petite image vous représentant. Elle est affichée sur votre profil,
	à gauche de vos messages sur le forum, de vos commentaires sur le blog, etc. Si l'avatar
	indiqué est trop grand, il sera automatiquement redimensionné à la taille maximale
	autorisée (100x100 pixels).<br />
	<span class="gras rouge">Les administrateurs se réservent le droit de supprimer
	tout avatar de nature à choquer la sensibilité des visiteurs.</span>
</p>

<form action="" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend>Modifier l'avatar</legend>
		<label for="avatar">Avatar actuel :</label>
		<div id="avatar_actuel">
			<?php if(!empty($InfosMembre['utilisateur_avatar'])){ ?>
			<a href="/membres/profil-<?php echo $_GET['id']; ?>-<?php echo rewrite($_SESSION['pseudo']); ?>.html"><img src="/uploads/avatars/<?php echo htmlspecialchars($InfosMembre['utilisateur_avatar']); ?>" title="Avatar actuel" alt="Avatar" /></a>
			<?php } else echo 'Pas d\'avatar'; ?>
		</div>

		<?php if(!empty($InfosMembre['utilisateur_avatar'])){ ?>
		<label for="avatar_suppr">Supprimer l'avatar :</label>
		<input type="checkbox" name="avatar_suppr" id="avatar_suppr"
		onclick="if(this.checked==true){
			$('chgt_avatar').setStyle('display', 'none');
			$('avatar_actuel').innerHTML = 'L\'avatar va être supprimé…';
		}
		else{
			$('chgt_avatar').setStyle('display', 'block');
			$('avatar_actuel').innerHTML = '<img src=\'/uploads/avatars/<?php echo htmlspecialchars($InfosMembre['utilisateur_avatar']); ?>\' title=\'Avatar actuel\' alt=\'Avatar\' />';
		}" /><br />
		<?php }	?>

		<div id="chgt_avatar">
			<label for="avatar">Changer l'avatar (choisir sur mon disque dur) :</label>
			<input type="file" name="avatar" id="avatar" /> OU<br /><br />

			<label for="avatar2">Changer l'avatar (entrez une adresse web) :</label>
			<input type="text" name="avatar2" id="avatar2" />
		</div><br />

		<input type="submit" name="submit" value="Envoyer" />
	</fieldset>

</form>
