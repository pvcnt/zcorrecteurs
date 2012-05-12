<?php $view->extend('::layouts/default.html.php') ?>

<h1>zCorrection</h1>

<?php if(verifier('zcorriger')){ ?>
<h2>Liste des documents que vous avez pris en charge</h2>

<?php
if (empty($ListerSoumissionsCorrecteur))
{
	echo '<p>Il n\'y a aucun document pour le moment.</p>';
}
else
{
?>
<table class="UI_items">
	<thead>
	<tr class="header_message">
		<th style="width: 8%;">Type</th>
		<th style="width: 15%;">Nom</th>
		<th style="width: 15%;">Auteur</th>
		<th style="width: 13%;">Soumission</th>
		<th>Messages</th>
		<th style="width: 15%;">Statut</th>
		<th style="width: 10%;">Actions</th>
	</tr>
	</thead>

	<tbody>
	<?php

	foreach ($ListerSoumissionsCorrecteur as $s)
	{
		echo $view->render('ZcoZcorrectionBundle::_ligne'.ucfirst($s['type']).'.html.php', array('s' => $s, 'type' => 'correcteur'));
	}
	?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="7">
				<span class="UI_errorrow">Document à corriger en priorité !</span><br />
				<span class="UI_inforow">Document nécessitant seulement une <span style="text-decoration: underline;">re</span>correction.</span><br />
			</td>
		</tr>
	</tfoot>
	</table>
<?php
}
}
?>

<?php if(verifier('voir_tutos_attente')){ ?>
<h2>Liste complète des documents à corriger</h2>

<?php

if (empty($ListerSoumissions))
{
	echo '<p>Il n\'y a aucun document pour le moment.</p>';
}
else
{
?>
<table class="UI_items">
	<thead>
	<tr class="header_message">
		<th style="width: 8%;">Type</th>
		<th style="width: 15%;">Nom</th>
		<th style="width: 15%;">Auteur</th>
		<th style="width: 13%;">Soumission</th>
		<th>Commentaires</th>
		<th style="width: 15%;">Statut</th>
		<th style="width: 10%;">Actions</th>
	</tr>
	</thead>

	<tbody>
	<?php
	foreach($ListerSoumissions as $s)
	{
		echo $view->render('ZcoZcorrectionBundle::_ligne'.ucfirst($s['type']).'.html.php', array('s' => $s, 'type' => 'liste'));
	}
	?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="7">
				<span class="UI_errorrow">Document à corriger en priorité !</span><br />
				<span class="UI_inforow">Document nécessitant seulement une <span style="text-decoration: underline;">re</span>correction.</span><br />
			</td>
		</tr>
	</tfoot>
	</table>
<?php
}
}
