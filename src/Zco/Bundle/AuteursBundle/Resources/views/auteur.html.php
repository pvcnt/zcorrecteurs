<?php $view->extend('::layouts/default.html.php') ?>

<h1><?php echo htmlspecialchars($Auteur->prenom.' '.$Auteur->nom) ?></h1>

<?php if (!empty($Auteur->autres)): ?>
	<p><?php echo $Auteur->autres ?></p>
<?php endif ?>

<?php if(!empty($Auteur->description)): ?>
<p class="UI_box">
	<?php echo $view['messages']->parse($Auteur->description, array(
	    'files.entity_id' => $Auteur['id'],
	    'files.entity_class' => 'Auteur',
	)) ?>
</p>
<?php endif ?>

<table class="UI_items simple">
	<?php foreach($Ressources as $res): ?>
	<tr>
		<td>
			<img src="/img/objets/<?php echo $res['objet'] ?>.png" alt="" />
			<a href="<?php printf($res['res_url'], $res['res_id'], rewrite($res['res_titre'])) ?>">
				<?php echo htmlspecialchars($res['res_titre']) ?>
			</a>
		</td>
		<td>
			<?php echo dateformat($res['res_date']) ?>
		</td>
	</tr>
	<?php endforeach ?>
</table>
