<?php $view->extend('::layouts/bootstrap.html.php') ?>

<div style="border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; margin-bottom: 5px; border: 1px solid #EEE; background-color: whiteSmoke; padding: 10px;">
	<div class="row-fluid">
		<div class="span2" style="text-align: center;">
			<div class="avatar" style="margin-bottom: 5px;">
				<img src="<?php echo $user->getAvatar() ?>" />
			</div>
			<?php echo $view->get('messages')->userGroup($user) ?>
		</div>
		<div class="span10">
			<div style="float: right; width: 210px; margin-right: 10px;">
				<?php echo $view->render('ZcoUserBundle:Profile:_actions.html.php', compact('user', 'own', 'canSendEmail', 'canSendMp', 'canAdmin')) ?>
			</div>

			<h1 style="margin-top: 0;">
				Profil d<?php echo $art.htmlspecialchars($user->getUsername()) ?>
				<?php if ($user->hasCitation()): ?>
					<small><?php echo htmlspecialchars($user->getCitation()) ?></small>
				<?php endif ?>
			</h1>

			<?php echo $view->render('ZcoUserBundle:Profile:_profile.html.php', compact('user', 'lastGroupChange')) ?>
		</div> <!-- /.span10 -->
	</div> <!-- /.row-fluid -->
</div>

<div style="margin-top: 20px;">
	<?php echo $view->render('ZcoUserBundle:Profile:_content.html.php', compact('user')) ?>
</div>

<?php if ($canSeeInfos): ?>
	<div style="margin-top: 20px;">
		<?php echo $view->render('ZcoUserBundle:Profile:_infos.html.php', compact('user', 'newPseudo', 'warnings', 'punishments', 'ListerGroupes', 'ListerIPs')) ?>
	</div>
<?php endif ?>