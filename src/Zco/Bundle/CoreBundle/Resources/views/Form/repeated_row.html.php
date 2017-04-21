<?php /* Sur un groupe de champs, affiche chaque champ comme contenant des 
erreurs mais n'affiche les erreurs que sur le dernier champ de la série. */
foreach ($form as $i => $child): ?>
	<?php if ($i === count($form) - 1) $child['errors'] = (count($errors) > 1) ? array_slice($errors, 1) : $errors ?>
    <?php echo $view['form']->row($child, array('errors' => $errors)) ?>
<?php endforeach; ?>
