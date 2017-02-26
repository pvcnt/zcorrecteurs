<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoTwitterBundle::tabs.html.php', array('currentTab' => 'accounts')) ?>

<p>
	Cette page permet de gérer les comptes Twitter qui seront utilisés pour envoyer
	des <em>tweets</em>.
</p>

<p>Les comptes suivants sont actuellement liés au site :</p>

<?php if (!count($accounts)): ?>
<p><em>Il n'y a actuellement aucun compte.</em>
<?php else: ?>
<form action="<?php echo $view['router']->path('zco_twitter_accounts') ?>" method="post">
<table class="table">
	<thead>
		<tr>
			<th style="width: 30%">Nom du compte</th>
			<th style="width: 20%">Date d'ajout</th>
			<th style="width: 20%">Nombre de <em>tweets</em></th>
			<th style="width: 20%">Dernier <em>tweet</em></th>
			<th style="width: 7%">Actions</th>
			<th style="width: 3%">
				<img src="/pix.gif"
				     alt="Par défaut"
				     class="fff tick"
				     title="Compte par défaut"
				/>
			</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($accounts as $account): ?>
		<tr id="c<?php echo $account['id'] ?>">
			<td>
				<a href="http://twitter.com/<?php
					echo rawurlencode($account['nom']) ?>">
					<?php echo htmlspecialchars($account['nom']) ?>
				</a>
			</td>
			<td><?php echo dateformat($account['creation']) ?></td>
			<td class="center"><?php echo $account['tweets'] ?></td>
			<td><?php echo dateformat($account->DernierTweet['creation']) ?></td>
			<td class="center">
				<a href="<?php echo $view['router']->path('zco_twitter_deleteAccount', array('id' => $account['id'])) ?>">
					<img src="/pix.gif"    alt="Supprimer"
					     class="fff cross" title="Supprimer"/>
				</a>
			</td>
			<td class="center">
				<input type="radio" title="Utiliser ce compte par défaut"
				       name="default_account" value="<?php echo $account['id'] ?>"
				       <?php if($account['par_defaut']) echo ' checked="checked"' ?>
				/>
			</td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<p style="text-align: right; width: 98%; margin: auto;margin-top: 5px">
	<a href="<?php echo $view['router']->path('zco_twitter_addAccount') ?>" class="btn btn-primary">Ajouter un compte</a>
	<input type="submit" class="btn" value="Modifier le compte par défaut"/>
</p>
</form>
<p>
	Le compte par défaut le sera pour les nouveaux <em>tweets</em>,
	et sera utilisé pour poster les messages traitant
	des mises à jour du blog.
</p>
<?php endif ?>
