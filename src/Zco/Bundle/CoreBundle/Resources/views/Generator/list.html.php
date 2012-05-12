<?php $view->extend('::layouts/default.html.php') ?>

<h1><?php echo $config['list']['title'] ?></h1>

<?php if (!empty($config['list']['description'])): ?>
	<p><?php echo $config['list']['description'] ?></p>
<?php endif; ?>

<?php if (!empty($config['list']['actions'])): ?>
<p class="gras centre">
	<?php $i = 0 ?>
	<?php foreach ($config['list']['actions'] as $action => $params): ?>
		<?php $route = isset($params['route']) ? $params['route'] : $config['actions'][$action]['route'] ?>
		<?php $credentials = isset($params['credentials']) ? $params['credentials'] : (isset($config['actions'][$action]['credentials']) ? $config['actions'][$action]['credentials'] : array()) ?>
		<?php $icon = isset($params['icon']) ? $params['icon'] : (isset($config['actions'][$action]['icon']) ? $config['actions'][$action]['icon'] : null) ?>
		<?php $label = isset($params['label']) ? $params['label'] : (isset($config['actions'][$action]['label']) ? $config['actions'][$action]['label'] : Util_Inflector::humanize($action)) ?>

		<?php if (verifier_array($credentials)): ?>
			<?php if($i > 0) echo ' - '; ?>
			<a href="<?php echo $route; ?>">
				<?php if (!is_null($icon)): ?><img src="<?php echo $icon ?>" alt="" /><?php endif; ?>
				<?php echo $label ?>
			</a>
			<?php $i++ ?>
		<?php endif; ?>
	<?php endforeach; ?>
</p>
<?php endif; ?>

<?php if (!empty($filters)): ?>
	<?php include(dirname(__FILE__).'/_filters.php'); ?>
<?php endif; ?>

<table class="generator-list" class="liste_cat" onclick="InverserEtat(event);" onmouseover="InverserEtat(event);" onmouseout="InverserEtat(event);">
	<thead>
		<?php if (false){ ?>
		<tr>
			<td colspan="<?php echo $colspan; ?>" class="centre">
				<?php include(dirname(__FILE__).'/_calendar.php'); ?>
			</td>
		</tr>
		<?php } ?>

		<?php if (isset($search)): ?>
		<tr>
			<td colspan="<?php echo $colspan; ?>">
				<form method="get" action="<?php echo $filters_url; ?>">
					<img src="/bundles/zcocore/img/generator/search.png" />
					<input type="text" name="<?php echo $search['name']; ?>" size="40" value="<?php echo !empty($search['value']) ? $search['value'] : $search['default']; ?>" onfocus="if(this.value == '<?php echo $search['default']; ?>') this.value='';" onblur="if(this.value == '') this.value='<?php echo $search['default']; ?>';" />

					<input type="submit" value="Rechercher" id="generator-rechercher"/>

					<?php if($paginator->count() != $count_objects_total){ ?>
					<span class="tpetit">
						<?php echo $paginator->count(); ?> résultat<?php echo pluriel($paginator->count()); ?>
						(<a href="?all"><?php echo $count_objects_total; ?>
							<?php echo lcfirst(pluriel($count_objects_total, $config['config']['plural'], $config['config']['plural'])); ?>
						au total</a>)
					</span>
					<?php } ?>
				</form>
			</td>
		</tr>
		<?php endif; ?>

		<?php if ($paginate == true): ?>
			<?php include dirname(__FILE__).'/_pagination.php'; ?>
		<?php endif; ?>

		<form id="form_batch" method="post" action="">
		<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />

		<tr>
			<?php if (!empty($config['list']['batch_actions'])): ?>
				<th><input type="checkbox" onclick="generator_switch_checkbox('form_batch');" /></th>
			<?php endif; ?>

			<?php foreach ($table['thead'] as $name => $field): ?>
				<?php if ($field['credentials'] == true): ?>
				<th <?php echo $field['attrs'] ?>>
					<a href="?_orderby=<?php echo (isset($_GET['_orderby']) && $_GET['_orderby'] == $name) ? '-'.$name : $name; ?>">
						<?php echo $field['label']; ?>

						<?php if (isset($_GET['_orderby']) && $_GET['_orderby'] == $name): ?>
							<img src="/bundles/zcocore/img/generator/arrow-down.gif" alt="Tri décroissant" />
						<?php elseif (isset($_GET['_orderby']) && $_GET['_orderby'] == '-'.$name): ?>
							<img src="/bundles/zcocore/img/generator/arrow-up.gif" alt="Tri croissant" />
						<?php endif; ?>
					</a>
				</th>
				<?php endif; ?>
			<?php endforeach; ?>

			<?php if (!empty($config['list']['object_actions'])): ?>
				<th style="min-width: 50px;">Actions</th>
			<?php endif; ?>
		</tr>
	</thead>

	<tfoot>
		<?php if ($paginate == true): ?>
		<?php include dirname(__FILE__).'/_pagination.php'; ?>
		<?php endif; ?>
	</tfoot>

	<tbody>
		<?php foreach ($objects as $row): ?>
		<tr class="sous_cat">
			<?php if (!empty($config['list']['batch_actions'])): ?>
				<td class="centre">
					<input type="checkbox" name="objects[<?php echo $row['id']; ?>]" id="objects[<?php echo $row['id']; ?>]" />
				</td>
			<?php endif; ?>

			<?php include(dirname(__FILE__).'/_list_'.$config['list']['layout'].'.php'); ?>

			<?php if (!empty($config['list']['object_actions'])): ?>
				<td class="centre">
					<?php foreach ($config['list']['object_actions'] as $action => $params): ?>
						<?php $route = isset($params['route']) ? $params['route'] : $config['actions'][$action]['route'] ?>
						<?php $credentials = isset($params['credentials']) ? $params['credentials'] : (isset($config['actions'][$action]['credentials']) ? $config['actions'][$action]['credentials'] : array()) ?>
						<?php $icon = isset($params['icon']) ? $params['icon'] : (isset($config['actions'][$action]['icon']) ? $config['actions'][$action]['icon'] : null) ?>
						<?php $label = isset($params['label']) ? $params['label'] : (isset($config['actions'][$action]['label']) ? $config['actions'][$action]['label'] : Util_Inflector::humanize($action)) ?>

						<?php if (verifier_array($credentials)): ?>
							<a href="<?php echo str_replace('%id%', $row['id'], $route) ?>">
								<?php echo !is_null($icon) ? sprintf('<img src="%s" alt="%s" title="%s" />', $icon, $label, $label) : $label ?>
							</a>
						<?php endif; ?>
					<?php endforeach; ?>
				</td>
			<?php endif; ?>
		</tr>
		<?php endforeach; ?>

		<tr>
			<td class="gras centre" colspan="<?php echo $colspan; ?>">
				<?php echo $paginator->count(); ?>
				<?php echo lcfirst(pluriel($paginator->count(), $config['config']['plural'], $config['config']['singular'])); ?>
			</td>
		</tr>
	</tbody>
</table>

<?php if(!empty($config['list']['batch_actions'])){ ?>
	<div class="droite" style="margin-top: 10px;">
		<?php include(dirname(__FILE__).'/_batch_actions.php'); ?>
	</div>
<?php } ?>
</form>
