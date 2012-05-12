<?php /* Sur un groupe de champs, affiche chaque champ comme contenant des 
erreurs mais n'affiche les erreurs que sur le dernier champ de la série. */
$i = 0; $c = count($form); foreach ($form as $child): ++$i; ?>
	<?php if ($i === $c) $child->set('errors', count($errors) > 1 ? array_slice($errors, 1) : $errors) ?>
    <?php echo $view['form']->row($child, array('errors' => $errors)) ?>
<?php endforeach; ?>
