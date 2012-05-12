<ul class="nav nav-tabs">
	<li<?php if ($currentTab === 'index') echo ' class="active"' ?>>
		<a href="<?php echo $view['router']->generate('zco_twitter_index') ?>">Dernier <em>tweets</em></a>
	</li>
	<li<?php if ($currentTab === 'newTweet') echo ' class="active"' ?>>
		<a href="<?php echo $view['router']->generate('zco_twitter_newTweet') ?>">Nouveau <em>tweet</em></a>
	</li>
	<li<?php if ($currentTab === 'mentions') echo ' class="active"' ?>>
		<a href="<?php echo $view['router']->generate('zco_twitter_mentions') ?>">
			Mentions
			<?php if (isset($mentions) && $mentions > 0): ?>
				<span class="badge"><?php echo $mentions ?></span>
			<?php endif ?>
		</a>
	</li>
	<?php if (verifier('twitter_comptes')): ?>
	<li style="float: right;"<?php if ($currentTab === 'accounts') echo ' class="active"' ?>>
		<a href="<?php echo $view['router']->generate('zco_twitter_accounts') ?>">Comptes</a>
	</li>
	<?php endif ?>
</ul>
