<?php if ($maintenance): ?>
    <div class="alert alert-error">
        Attention, le site est actuellement en maintenance !
    </div>
<?php endif ?>

<?php if (!empty($_SESSION['erreur'])): ?>
	<?php foreach ($_SESSION['erreur'] as $erreur): ?>
	    <div class="alert alert-error"><?php echo $erreur ?></div>
    <?php endforeach ?>
	<?php $_SESSION['erreur'] = array() ?>
<?php endif ?>

<?php if (!empty($_SESSION['message'])): ?>
	<?php foreach ($_SESSION['message'] as $message): ?>
		<div class="alert alert-success"><?php echo $message ?></div>
    <?php endforeach ?>
	<?php $_SESSION['message'] = array() ?>
<?php endif ?>