<?php $view->extend('::layouts/default.html.php') ?>

<h1>Voir les documents actifs</h1>

<p>
	Cette page regroupe de façon synthétique les documents en cours de zCorrection, 
	ou bien non pris en charge. Vous pouvez de la sorte agir dessus facilement.
</p>

<?php
if (empty($ListerSoumissionsAdmin))
{
	echo '<p>Il n\'y a aucun document en correction pour le moment.</p>';
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
			<th>Corrections</th>
			<th style="width: 20%;">Actions</th>
		</tr>
	</thead>

	<tbody>
		<?php
		$i = 0;
		foreach($ListerSoumissionsAdmin as $s)
		{
			echo $this->render('ZcoZcorrectionBundle::_ligne'.ucfirst($s['type']).'.html.php', array('s' => $s, 'type' => 'admin'));
		}
		?>
	</tbody>

	<tfoot>
		<tr>
			<td colspan="12"> Légende : <br />
			<span class="UI_errorrow">Document à corriger en priorité !</span><br />
			<span class="UI_inforow">Document nécessitant seulement une <span style="text-decoration: underline;">re</span>correction.</span></td>
		</tr>
	</tfoot>
</table>
<?php } ?>
