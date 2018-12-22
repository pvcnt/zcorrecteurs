<ul class="nav nav-tabs">
	<li<?php if ('profile' === $tab): ?> class="active"<?php endif ?>>
		<a href="<?php echo $view['router']->path('zco_options_profile', array('id' => $id)) ?>">
			Profil
		</a>
	</li>
	<li<?php if ('avatar' === $tab): ?> class="active"<?php endif ?>>
		<a href="<?php echo $view['router']->path('zco_options_avatar', array('id' => $id)) ?>">
			Avatar
		</a>
	</li>
	<li<?php if ('email' === $tab): ?> class="active"<?php endif ?>>
		<a href="<?php echo $view['router']->path('zco_options_email', array('id' => $id)) ?>">
			Adresse courriel
		</a>
	</li>
	<li<?php if ('password' === $tab): ?> class="active"<?php endif ?>>
		<a href="<?php echo $view['router']->path('zco_options_password', array('id' => $id)) ?>">
			Mot de passe
		</a>
	</li>
	<li<?php if ('preferences' === $tab): ?> class="active"<?php endif ?>>
		<a href="<?php echo $view['router']->path('zco_options_preferences', array('id' => $id)) ?>">
			Préférences
		</a>
	</li>
</ul>