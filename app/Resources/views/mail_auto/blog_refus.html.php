<p>Bonjour <strong><?php echo htmlspecialchars($pseudo) ?></strong> !</p>

<p>
	Vous avez proposé un billet sur le blog du site. Un administrateur du site
	a refusé votre billet. Nous vous remercions tout de même de l'avoir proposé.
	Vous pouvez bien entendu toujours en reproposer un par la suite, en tenant
	compte des remarques formulées par l'administrateur.
</p>

<p><strong>Raison donnée par l'administrateur :</strong><br />
<?php echo nl2br(htmlspecialchars($raison)) ?></p>

<ul>
	<li><a href="<?php echo URL_SITE ?>/apropos/contact">Contacter les administrateurs</a></li>
	<li><a href="<?php echo URL_SITE ?>/blog/mes-billets.html">Voir mes billets</a></li>
</ul>

<p>Cordialement,<br />
<em>L'équipe des zCorrecteurs.</em></p>
