<div id="generator-filters">
	<h2>Filtres</h2>

	<?php foreach ($filters as $filter): ?>
	<h3>Par <?php echo lcfirst($filter['label']); ?></h3>
	<ul>
		<li<?php if (!isset($_GET[$filter['name']])) echo ' class="selected"'; ?>>
			<a href="<?php echo str_replace($filter['url'], '', $filters_url); ?>">Tout</a>
		</li>
		<?php foreach ($filter['choices'] as $value => $link): ?>
		<li<?php if (isset($_GET[$filter['name']]) && $_GET[$filter['name']] == $value) echo ' class="selected"'; ?>>
			<?php if (!empty($filter['url'])): ?>
			<a href="<?php echo str_replace($filter['url'], sprintf('%s=%s', $filter['name'], $value), $filters_url); ?>">
			<?php else: ?>
			<a href="<?php printf('%s&%s=%s', $filters_url, $filter['name'], $value); ?>">
			<?php endif; ?>
				<?php echo $link; ?>
			</a>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endforeach; ?>
</div>