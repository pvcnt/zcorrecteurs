<ul class="nav nav-tabs">
	<li<?php if ($currentTab === 'mentions') echo ' class="active"' ?>>
		<a href="<?php echo $view['router']->generate('zco_info_static_mentions') ?>">
			Mentions légales
		</a>
	</li>
	<li<?php if ($currentTab === 'privacy') echo ' class="active"' ?>>
		<a href="<?php echo $view['router']->generate('zco_info_static_privacy') ?>">
			Politique de confidentialité
		</a>
	</li>
	<li<?php if ($currentTab === 'rules') echo ' class="active"' ?>>
		<a href="">Règlement</a>
	</li>
</ul>