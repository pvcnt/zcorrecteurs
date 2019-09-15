<?php if ($own): ?>
<div style="margin-top: 5px;">
	<a class="btn btn-success" 
	   href="<?php echo $view['router']->path('zco_options_index') ?>"
	   style="display: inline-block; width: 196px;"
	>
		<i class="icon-cog icon-white"></i> 
		Modifier mes options
	</a>
</div>
<?php else: ?>
<div>
	<a class="btn btn-success<?php if (!$canSendEmail): ?> disabled<?php endif ?>" 
		style="display: inline-block; width: 196px;"
	    <?php if ($canSendEmail): ?>
	    href="mailto:<?php echo $view['humanize']->email($user->getEmail()) ?>"
		<?php else: ?>
		href="#" onclick="return false;"
		<?php endif ?>
	>
		<i class="icon-envelope icon-white"></i> 
		Envoyer un courriel
        <?php if (verifier('rechercher_mail')): ?><br />
            <span style="font-size: 0.9em;"><?php echo htmlspecialchars($user->getEmail()) ?></span>
        <?php endif ?>
	</a>
</div>
<?php endif ?>

<?php if ($canAdmin): ?>
<div style="margin-top: 5px;"><div class="btn-group">
    <a class="btn dropdown-toggle" style="display: inline-block; width: 200px;" data-toggle="dropdown" href="#">
    	<i class="icon-wrench"></i>
    	Administrer le compte
    	<span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
		<?php if (verifier('groupes_changer_membre')): ?>
		<li>
			<a href="<?php echo $view['router']->path('zco_groups_assign', ['id' => $user->getId()]) ?>">
				Changer de groupe
			</a>
		</li>
		<?php endif ?>
		<?php if (verifier('options_editer_profils')): ?>
		<li>
			<a href="<?php echo $view['router']->path('zco_options_profile', array('id' => $user->getId())) ?>">
				Modifier ses paramètres
			</a>
		</li>
		<?php endif ?>
        <?php if (verifier('suppr_comptes')): ?>
        <li>
            <a href="<?php echo $view['router']->path('zco_user_admin_deleteAccount', array('id' => $user->getId())) ?>">
                Supprimer le compte
            </a>
        </li>
        <?php endif ?>
    </ul>
</div></div>
<?php endif ?>