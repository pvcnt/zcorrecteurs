<?php $view->extend('::layouts/default.html.php') ?>

<h1><?php echo htmlspecialchars($tag['nom']) ?></h1>

<table class="UI_items simple">
	<?php foreach($objects as $object) { ?>
	<tr>
		<td>
			<img src="/img/objets/<?php echo $object['objet'] ?>.png" alt="" />
			<a href="<?php printf($object['res_url'], $object['res_id'], rewrite($object['res_titre'])) ?>">
				<?php echo htmlspecialchars($object['res_titre']) ?>
			</a>
		</td>
		<td>
			<?php echo dateformat($object['res_date']) ?>
		</td>
	</tr>
	<?php } ?>
</table>
