<?php $view->extend('::layouts/default.html.php') ?>

<h1>Messages postés par <?php echo htmlspecialchars($membre->pseudo) ?></h1>

<table class="UI_items messages">
	<thead>
		<tr>
			<td colspan="2">Page : <?php echo $view['ui']->render($pager) ?></td>
		</tr>
		<tr>
			<th style="width: 13%;">Auteur</th>
			<th style="width: 87%;">Message</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="2">Page : <?php echo $view['ui']->render($pager) ?></td>
		</tr>
	</tfoot>

	<tbody>
		<?php foreach ($pager as $message): ?>
		<tr class="header_message">
			<td class="pseudo_membre">
				<a href="/membres/profil-<?php echo $membre->id
				?>-<?php echo rewrite($membre->pseudo) ?>.html"
					style="color: <?php echo $membre->Groupe['class'] ?>;">
					<?php echo htmlspecialchars($membre->pseudo) ?>
				</a>
			</td>
			<td class="dates">
				Posté <?php echo dateformat($message->date, MINUSCULE) ?> -
				<?php if ($message->Sujet->resolu): ?>
				<img src="/bundles/zcoforum/img/resolu.png" alt="Sujet résolu" />
				<?php endif; if ($message->Sujet->ferme): ?>
				<img src="/bundles/zcoforum/img/cadenas.png" alt="Sujet fermé" />
				<?php endif ?>

				<strong>
					<a href="/forum/sujet-<?php echo $message->Sujet->id
					?>-<?php echo $message->id
					?>-<?php echo rewrite($message->Sujet->titre) ?>.html">
						<?php echo htmlspecialchars($message->Sujet->titre) ?>
					</a>
				</strong>
			</td>
		</tr>

		<tr>
			<td class="infos_membre">
				<?php if ($membre->hasAvatar()): ?>
				<img src="<?php echo $membre->getAvatarUrl() ?>"
				alt="<?php echo 'Avatar de '.htmlspecialchars($membre->pseudo) ?>" />
				</a><br />
				<?php endif ?>

				<?php if ($membre->Groupe->logo): ?>
					<img src="<?php
						if ($membre->sexe == SEXE_FEMININ)
							echo htmlspecialchars($membre->Groupe->logo_feminin);
						else
							echo htmlspecialchars($membre->Groupe->logo);
					?>"
					     alt="<?php echo htmlspecialchars($membre->Groupe->nom) ?>"
				<?php endif ?>
			</td>

			<td class="message">
				<div class="msgbox">
					<?php echo $view['messages']->parse($message->texte, array(
						'core.anchor_prefix' => $message->id,
						'files.entity_id' => $message->id,
						'files.entity_class' => 'ForumMessage',
					)) ?>
				</div>
			</td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
