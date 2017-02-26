<ul class="nav nav-tabs">
	<li<?php if ($currentTab === 'mentions') echo ' class="active"' ?>>
		<a href="<?php echo $view['router']->path('zco_legal_mentions') ?>">
			Mentions légales
		</a>
	</li>
	<li<?php if ($currentTab === 'privacy') echo ' class="active"' ?>>
		<a href="<?php echo $view['router']->path('zco_legal_privacy') ?>">
			Politique de confidentialité
		</a>
	</li>
    <li<?php if ($currentTab === 'rules') echo ' class="active"' ?>>
        <a href="<?php echo $view['router']->path('zco_legal_rules') ?>">
            Règlement
        </a>
	</li>
</ul>