<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Administration</h1>

<div class="admin-wrapper">
	<?php echo $admin ?>
</div>

<?php $view['vitesse']->requireResource('@ZcoAdminBundle/Resources/public/css/admin.css') ?>
<?php $view['javelin']->initBehavior('admin-homepage');