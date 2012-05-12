<?php $view->extend('::layouts/default.html.php') ?>

<h1>Classement Alexa</h1>

<?php
$i18nMois = array(1 => 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
	'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
$i18nJours = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
?>

<form method="get">
	<fieldset>
		<legend>Sélectionner une date</legend>
		<label for="input_annee">Année</label>
		<input type="text" name="annee" id="input_annee" value="<?php echo $Annee ?>"/>
		<br/>

		<label for="input_mois">Mois</label>
		<select name="mois" id="input_mois">
			<option value="">&nbsp;- Toute l'année -&nbsp;</option>
			<?php foreach($i18nMois as $i => $m): ?>
			<option value="<?php echo $i ?>"<?php
			if($i == $Mois) echo ' selected="selected"' ?>><?php echo $m ?></option>
			<?php endforeach ?>
		</select>
		<br/>

		<input type="submit" value="Atteindre"/>
	</fieldset>
</form>

<p style="text-align: center">
	<img src="alexa-1.html?annee=<?php echo $Annee ?>&amp;mois=<?php echo $Mois
	?>" alt="Évolution du classement du site"/>
</p>

<?php
$maxF = $maxG = 0;
$minF = $minG = $Rangs[0]['rang_global'];

foreach ($Rangs as $r)
{
	if ($r['rang_france'] > $maxF)
		$maxF = $r['rang_france'];
	if ($r['rang_global'] > $maxG)
		$maxG = $r['rang_global'];

	if ($r['rang_france'] < $minF)
		$minF = $r['rang_france'];
	if ($r['rang_global'] < $minG)
		$minG = $r['rang_global'];
}
?>

<?php if ($Mois === null): ?>
<table class="UI_items">
	<caption>Statistiques pour l'année <?php echo $Annee ?></caption>
	<thead>
		<tr>
			<th>Mois</th>
			<th>Rang (France)</th>
			<th>Rang (Mondial)</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($Rangs as $r): ?>
		<tr>
			<td><a href="?annee=<?php echo $Annee ?>&amp;mois=<?php echo $r['mois'] ?>">
				<?php echo $i18nMois[$r['mois']] ?></a></td>
			<td<?php if ($r['rang_france'] === $minF) echo ' style="font-weight:bold;color:blue"';
				elseif ($r['rang_france'] === $maxF) echo ' style="font-weight:bold;color:red"';
			?>><?php echo $r['rang_france'] ?></td>
			<td<?php if ($r['rang_global'] === $minG) echo ' style="font-weight:bold;color:blue"';
				elseif ($r['rang_global'] === $maxG) echo ' style="font-weight:bold;color:red"';
			?>><?php echo $r['rang_global'] ?></td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?php else: ?>

<?php
$d = mktime(0, 0, 0, $Mois, 1, $Annee);
$d = date('N', $d) - 1;
?>

<table class="UI_items">
	<caption>Statistiques pour <?php echo $i18nMois[$Mois] ?> <?php echo $Annee ?></caption>
	<thead>
		<tr>
			<th>Jour</th>
			<th>Rang (France)</th>
			<th>Rang (Mondial)</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($Rangs as $r): ?>
		<tr>
			<td><?php echo $i18nJours[($r['jour'] % 7) - 1 + $d] ?> <?php echo $r['jour'] ?></td>
			<td<?php if ($r['rang_france'] === $minF) echo ' style="font-weight:bold;color:blue"';
				elseif ($r['rang_france'] === $maxF) echo ' style="font-weight:bold;color:red"';
			?>><?php echo $r['rang_france'] ?></td>
			<td<?php if ($r['rang_global'] === $minG) echo ' style="font-weight:bold;color:blue"';
				elseif ($r['rang_global'] === $maxG) echo ' style="font-weight:bold;color:red"';
			?>><?php echo $r['rang_global'] ?></td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?php endif ?>
