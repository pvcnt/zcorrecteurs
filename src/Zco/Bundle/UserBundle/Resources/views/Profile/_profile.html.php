<ul style="list-style-type: none; margin-left: 0;">
	<li>
		<?php $c = 0; if ($user->hasBirthDate()): ?>
			<i class="icon-gift"></i>
			<span class="gris">Âgé de</span>
			<strong><?php echo $user->getAge() ?> ans</strong>
		<?php ++$c; endif ?>
		<?php if ($user->isCountryDisplayed() && $user->hasLocalisation()): ?>
			<?php if ($c > 0): ?>|<?php endif ?>
			<i class="icon-plane"></i> 
			<span class="gris">Vit non loin de</span>
			<strong><?php echo htmlspecialchars($user->getLocalisation()) ?></strong>
		<?php ++$c; endif ?>
		<?php if ($user->hasJob()): ?>
			<?php if ($c > 0): ?>|<?php endif ?>
			<i class="icon-briefcase"></i> 
			<span class="gris"><?php echo htmlspecialchars($user->getJob()) ?></span>
		<?php ++$c; endif ?>
	</li>
	<?php if ($user->hasHobbies()): ?>
	<li>
		<i class="icon-heart"></i> 
		<span class="gris">Intéressé par</span>
		<strong><?php echo htmlspecialchars($user->getHobbies()) ?></strong>
	</li>
	<?php endif ?>
	<?php if ($user->hasWebsite() || $user->hasTwitter()): ?>
	<li>
		<i class="icon-globe"></i>
		<?php if ($user->hasWebsite()): ?>
			<span class="gris">Présent sur</span>
			<?php if (filter_var($user->getWebsite(), FILTER_VALIDATE_URL)): ?>
				<a href="<?php echo htmlspecialchars($user->getWebsite()) ?>">
					<?php echo htmlspecialchars(preg_replace('#^https?://#', '', $user->getWebsite())) ?>
				</a>
			<?php else: ?>
				<?php echo htmlspecialchars($user->getWebsite()) ?>
			<?php endif ?>
		<?php endif ?>
		<?php if ($user->hasTwitter()): ?>
			<?php if ($user->hasWebsite()): ?>
				<span class="gris">et sur Twitter <em>via</em></span> 
			<?php else: ?>
				<span class="gris">Présent sur Twitter <em>via</em></span> 
			<?php endif ?>
			<a href="http://twitter.com/<?php echo htmlspecialchars($user->getTwitter()) ?>">@<?php echo htmlspecialchars($user->getTwitter()) ?></a>
		<?php endif ?>
	</li>
	<?php endif ?>
</ul>
<ul style="list-style-type: none; margin-left: 0;">
	<li>
		<i class="icon-user"></i>
		<span class="gris">Inscrit </span>
		<strong><?php echo dateformat($user->getRegistrationDate(), MINUSCULE, DATE) ?></strong><?php if ($user->getLastActionDate()): ?>
		<span class="gris">et venu pour la dernière fois</span> 
		<strong><?php echo dateformat($user->getLastActionDate(), MINUSCULE, DATE) ?></strong><?php endif ?><span class="gris">.</span>
	</li>
	<?php if ($user->isTeam()): ?>
	<li>
		<i class="icon-ok"></i>
		<span class="gris">Membre des «&nbsp;</span><span style="font-weight: bold;"><?php echo htmlspecialchars($user->getGroup()) ?></span><span class="gris">&nbsp;»
		depuis <?php echo dateformat($lastGroupChange, MINUSCULE, DATE) ?><?php if ($user->hasTitle()): ?> et « </span>
		<strong><?php echo htmlspecialchars($user->getTitle()) ?></strong>
		<span class="gris"> »<?php endif ?>.</span>
	</li>
	<?php endif ?>
	<?php if (verifier('voir_groupes_secondaires') && ($c = count($user->SecondaryGroups))): ?>
	<li>
		<i class="icon-info-sign"></i>
		<span class="gris">Également membre de </span>
		<?php foreach ($user->getSecondaryGroups() as $i => $group): ?>
			«&nbsp;<span style="font-weight: bold;"><?php echo htmlspecialchars($group->getGroup()) ?></span>&nbsp;»<?php if ($i != $c - 1): ?>, <?php else: ?>.<?php endif ?>
		<?php endforeach ?>
	</li>
	<?php endif ?>
</ul>