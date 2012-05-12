<?php $view->extend('::layouts/default.html.php') ?>

<h1><?php echo htmlspecialchars($Tag->nom); ?></h1>

<?php if(!empty($Tag->description)){ ?>
<p class="UI_box">
	<?php echo $view['messages']->parse($Tag->description); ?>
</p>
<?php } ?>

<table class="UI_items simple">
	<?php foreach($Ressources as $res){ ?>
	<tr>
		<td>
			<img src="/img/objets/<?php echo $res['objet']; ?>.png" alt="" />
			<a href="<?php printf($res['res_url'], $res['res_id'], rewrite($res['res_titre'])); ?>">
				<?php echo htmlspecialchars($res['res_titre']); ?>
			</a>
		</td>
		<td>
			<?php echo dateformat($res['res_date']); ?>
		</td>
	</tr>
	<?php } ?>
</table>
