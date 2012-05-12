<p>Bonjour <strong><?php echo htmlspecialchars($pseudo) ?></strong> !</p>

<p>
	Vous avez proposé un billet sur le blog du site. Un administrateur du site a
	validé votre billet ! Nous vous remercions de l'avoir proposé. Vous pouvez
	bien entendu toujours en reproposer un autre par la suite si vous le désirez.<br />
	Nous vous remercions de contribuer activement à la vie du site !
</p>

<p><strong>Raison donnée par l'administrateur :</strong><br />
<?php echo nl2br(htmlspecialchars($raison)) ?></p>

<ul>
	<li><a href="<?php echo URL_SITE ?>/apropos/contact">Contacter les administrateurs</a></li>
	<li><a href="<?php echo URL_SITE ?>/blog/mes-billets.html">Voir mes billets</a></li>
	<li><a href="<?php echo URL_SITE ?>/blog/">Accéder au blog</a></li>
</ul>

<p>Cordialement,<br />
<em>L'équipe des zCorrecteurs.</em></p>

