<?php foreach($table['tbody'] as $name => $field): ?>
	<?php if ($field['credentials']): ?>
		<td <?php echo $field['attrs'] ?>>
			<?php if($field['is_link']): ?>
				<a href="<?php echo str_replace('%id%', $row['id'], $config['actions']['_edit']['route']) ?>">
					<?php eval($field['content']) ?>
				</a>
			<?php else: ?>
				<?php eval($field['content']); ?>
			<?php endif; ?>
		</td>
	<?php endif; ?>
<?php endforeach; ?>
