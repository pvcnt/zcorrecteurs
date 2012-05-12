<?php $view->extend('::layouts/default.html.php') ?>

<h1>Voir les tutoriels</h1>

<p>
	Cette page vous permet d'effectuer diverses recherches parmi les tutoriels en cours de correction ou corrigés.<br />
	<strong><?php echo $CompterSoumissions; ?> tutoriel<?php echo pluriel($CompterSoumissions); ?></strong>
	<?php if($Etat == CORRECTION){ ?>
	en cours de correction par
	<?php echo is_null($id) ? 'l\'équipe' : '<a href="/membres/profil-'.$id.'-'.rewrite($InfosMembre['utilisateur_pseudo']).'.html">'.htmlspecialchars($InfosMembre['utilisateur_pseudo']).'</a>'; ?>
	<?php echo pluriel($CompterSoumissions, 'ont', 'a'); ?> été trouvé<?php echo pluriel($CompterSoumissions); ?>.
	<?php } elseif($Etat == TERMINE_CORRIGE){ ?>
	corrigé<?php echo pluriel($CompterSoumissions); ?> par
	<?php echo is_null($id) ? 'l\'équipe' : '<a href="/membres/profil-'.$id.'-'.rewrite($InfosMembre['utilisateur_pseudo']).'.html">'.htmlspecialchars($InfosMembre['utilisateur_pseudo']).'</a>'; ?>
	<?php echo pluriel($CompterSoumissions, 'ont', 'a'); ?> été trouvé<?php echo pluriel($CompterSoumissions); ?>.
	<?php } else{ ?>
	corrigé<?php echo pluriel($CompterSoumissions); ?> ou en cours de correction par
	<?php echo is_null($id) ? 'l\'équipe' : '<a href="/membres/profil-'.$id.'-'.rewrite($InfosMembre['utilisateur_pseudo']).'.html">'.htmlspecialchars($InfosMembre['utilisateur_pseudo']).'</a>'; ?>
	<?php echo pluriel($CompterSoumissions, 'ont', 'a'); ?> été trouvé<?php echo pluriel($CompterSoumissions); ?>.
	<?php } ?>
</p>

<form method="post" action="corrections.html">
	<fieldset>
		<legend>Rechercher parmi les tutoriels</legend>

		<label for="etat">&Eacute;tat : </label>
		<select name="etat" id="etat">
			<option value="<?php echo ALL; ?>"<?php if($Etat == ALL) echo ' selected="selected"'; ?>>N'importe</option>
			<option value="<?php echo CORRECTION; ?>"<?php if($Etat == CORRECTION) echo ' selected="selected"'; ?>>En cours de zCorrection</option>
			<option value="<?php echo TERMINE_CORRIGE; ?>"<?php if($Etat == TERMINE_CORRIGE) echo ' selected="selected"'; ?>>zCorrigé</option>
		</select><br />
		<label for="nom">Fragment du nom du tutoriel : </label>
		<input type="text" id="nom" name="nom" value="<?php if(isset($_POST['nom'])) echo htmlspecialchars($_POST['nom']); ?>" /><br />
		<label for="auteur">Auteur : </label>
		<input type="text" id="auteur" name="auteur" value="<?php if(!is_null($id2)) echo htmlspecialchars($InfosMembre2['utilisateur_pseudo']); ?>" /><br />
		<label for="zco">Correcteur ou recorrecteur : </label>
		<input type="text" id="zco" name="zco" value="<?php if(!is_null($id)) echo htmlspecialchars($InfosMembre['utilisateur_pseudo']); ?>" /><br />
		<input type="submit" value="Envoyer" />
	</fieldset>
</form>

<?php
if (empty($ListerSoumissions))
{
	echo '<p>Aucun tutoriel n\'a été trouvé.</p>';
}
else
{
?>
<table class="UI_items">
	<thead>
		<tr>
			<td colspan="6">Page :
				<?php foreach($ListePages as $element) echo $element.''; ?>
			</td>
		</tr>
		<tr class="header_message">
			<th>Type</th>
			<th>Nom du tutoriel</th>
			<th>Validateur</th>
			<th>Auteur</th>
			<th>Soumission</th>
			<th>&Eacute;tat</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="6">Page :
				<?php foreach($ListePages as $element) echo $element.''; ?>
			</td>
		</tr>
	</tfoot>

	<tbody>
	<?php
	$i = 0;
	foreach($ListerSoumissions as $s)
	{
		$liste_types = array(MINI_TUTO => 'Mini', BIG_TUTO => 'Big');

		$titre = '';
		$description = trim($s['soumission_description']);

		if (empty($description))
		{
			$description = 'Pas de description.';
		}

		if (MINI_TUTO == $s['soumission_type_tuto'])
		{
			$titre = $s['mini_tuto_titre'];
		}
		else if (BIG_TUTO == $s['soumission_type_tuto'])
		{
			$titre = $s['big_tuto_titre'];
		}

		echo '<tr>
			<td class="centre">'.$liste_types[$s['soumission_type_tuto']].'</td>
			<td><a href="/zcorrection/fiche-tuto-'.$s['soumission_id'].'.html">'.htmlspecialchars($titre).'</a> ('.round(@filesize(BASEPATH.'/web/tutos/'.$s['soumission_sauvegarde'])/1000, 2).' ko)</td>
			<td class="centre">';
		    if(!empty($s['valido_idsdz']))
		    {
		        echo '<a href="http://www.siteduzero.com/membres-294-'.$s['valido_idsdz'].'.html">'.htmlspecialchars($s['valido_pseudo']).'</a>';
		    }
		    else
		    {
		        echo ' - ';
		    }
		    echo '</td>
			<td class="centre">';
			if(!empty($s['tutoteur_pseudo']))
			{
			    echo '<a href="http://www.siteduzero.com/membres-294-'.$s['tutoteur_idsdz'].'.html">'.htmlspecialchars($s['tutoteur_pseudo']).'</a>';
			}
			else
			{
			    echo '<a href="/membres/profil-'.$s['tutoteur_idsdz'].'-'.rewrite($s['soumission_tutoteur_pseudo_ancien_systeme']).'.html">'.htmlspecialchars($s['soumission_tutoteur_pseudo_ancien_systeme']).'</a>';
			}
			echo '
			</td>
			<td class="centre">'.dateformat($s['soumission_date']).'</td>
			<td class="centre">';

			if (empty($s['correction_id'])){
				echo 'Correction non prise en charge.';
			}
			elseif (empty($s['correction_date_debut']))
			{
				echo 'Correction commencée par <a href="/membres/profil-'.$s['id_correcteur'].'-'.rewrite($s['pseudo_correcteur']).'.html">'.htmlspecialchars($s['pseudo_correcteur']).'</a>';
				if($s['correction_abandonee'] == 1) echo ' - Abandonnée';
				echo '.';
			}
			elseif (empty($s['correction_date_fin']))
			{
				echo 'Correction commencée '.dateformat($s['correction_date_debut'], MINUSCULE).' par <a href="/membres/profil-'.$s['id_correcteur'].'-'.rewrite($s['pseudo_correcteur']).'.html">'.htmlspecialchars($s['pseudo_correcteur']).'</a>';
				if($s['correction_abandonee'] == 1) echo ' - Abandonnée';
				echo '.';
			}
			elseif (empty($s['recorrection_date_debut']) && $s['soumission_recorrection'] == 1)
			{
				echo 'Correction terminée '.dateformat($s['correction_date_fin'], MINUSCULE).' par <a href="/membres/profil-'.$s['id_correcteur'].'-'.rewrite($s['pseudo_correcteur']).'.html">'.htmlspecialchars($s['pseudo_correcteur']).'</a>.<br />
Recorrection prise en charge par <a href="/membres/profil-'.$s['id_recorrecteur'].'-'.rewrite($s['pseudo_recorrecteur']).'.html">'.htmlspecialchars($s['pseudo_recorrecteur']).'</a>';
				if($s['recorrection_abandonee'] == 1) echo ' - Abandonnée';
				echo '.';

			}
			elseif (empty($s['recorrection_date_fin']) && $s['soumission_recorrection'] == 1)
			{
				echo 'Correction terminée '.dateformat($s['correction_date_fin'], MINUSCULE).' par <a href="/membres/profil-'.$s['id_correcteur'].'-'.rewrite($s['pseudo_correcteur']).'.html">'.htmlspecialchars($s['pseudo_correcteur']).'</a>.<br />
				Recorrection commencée '.dateformat($s['recorrection_date_debut'], MINUSCULE).' par <a href="/membres/profil-'.$s['id_recorrecteur'].'-'.rewrite($s['pseudo_recorrecteur']).'.html">'.htmlspecialchars($s['pseudo_recorrecteur']).'</a>';
				if($s['recorrection_abandonee'] == 1) echo ' - Abandonnée';
				echo '.';
			}
			else
			{
				echo 'Correction terminée '.dateformat($s['correction_date_fin'], MINUSCULE).' par <a href="/membres/profil-'.$s['id_correcteur'].'-'.rewrite($s['pseudo_correcteur']).'.html">'.htmlspecialchars($s['pseudo_correcteur']).'</a>.';
				if (!empty($s['recorrection_date_fin']))
				{
					echo '<br />Recorrection terminée '.dateformat($s['recorrection_date_fin'], MINUSCULE).' par <a href="/membres/profil-'.$s['id_recorrecteur'].'-'.rewrite($s['pseudo_recorrecteur']).'.html">'.htmlspecialchars($s['pseudo_recorrecteur']).'</a>.';
				}
			}
		echo '</td>';
		echo '</tr>';
	}
	?>
	</tbody>
	</table>
<?php
}
?>
