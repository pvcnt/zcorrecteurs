<ul class="nav nav-tabs">
	<li<?php if ($currentTab === 'index') echo ' class="active"'; ?>>
		<a href="<?php echo $view['router']->generate('zco_about_index') ?>">À propos</a>
	</li>
	<li<?php if ($currentTab === 'team') echo ' class="active"'; ?>>
		<a href="<?php echo $view['router']->generate('zco_about_team') ?>">Notre équipe</a>
	</li>
	<li<?php if ($currentTab === 'corrigraphie') echo ' class="active"'; ?>>
		<a href="<?php echo $view['router']->generate('zco_about_corrigraphie') ?>">Notre association</a>
	</li>
	<li<?php if ($currentTab === 'banners') echo ' class="active"'; ?>>
		<a href="<?php echo $view['router']->generate('zco_about_banners') ?>">Bannières</a>
	</li>
	<?php /*<li<?php if ($currentTab === 'opensource') echo ' class="active"'; ?>>
		<a href="<?php echo $view['router']->generate('zco_about_opensource') ?>">Logiciel libre</a>
	</li>*/ ?>
	<li<?php if ($currentTab === 'contact') echo ' class="active"'; ?>>
		<a href="<?php echo $view['router']->generate('zco_about_contact') ?>">Contact</a>
	</li>
</ul>


