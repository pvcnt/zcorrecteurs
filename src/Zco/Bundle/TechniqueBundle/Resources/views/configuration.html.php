<?php $view->extend('::layouts/default.html.php') ?>

<h1>Configuration</h1>

<?php
if(isset($journalModif) && count($journalModif) > 0) {
	echo '<div class="journal_config">Les changements effectués sont :<dl>';
	foreach($journalModif as $const => $avant_apres) {
		echo '<dt>' . htmlentities($const) . '</dt><dd>';
		if(is_bool($avant_apres[0]) && is_bool($avant_apres[1])) {
			echo (($avant_apres[0]) ? '<i>vrai</i>' : '<i>faux</i>') . ' &#8658; ' .
				 (($avant_apres[1]) ? '<i>vrai</i>' : '<i>faux</i>');
		} else
			echo htmlentities($avant_apres[0]) . ' &#8658; ' . htmlentities($avant_apres[1]);
		echo '</dd></dt>';
	}
	echo '</dl></div>';
}
?>

<p>
	Vous pouvez modifier certains paramètres du site, modifiant son affichage ou son fonctionnement interne.
</p>

<p>
<?php
foreach(array_keys($configuration) as $key)
	echo '<a href="#' . $key . '">' . $groupes[$key] . '</a> | ';
?>
</p>

<form method="post" action="">
	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>

<?php
foreach($configuration as $key => $value) {
	if(count($value) < 1)
		continue;
	echo '<a name="' . $key . '"></a>';
	echo '<fieldset><legend>' . $groupes[$key] . '</legend>';
	foreach($value as $const => $attrib) {
		echo '<label for="' . (($attrib['type'] == T_BOOL) ? 'ch['. $const . ']' : $const) . '">' . $attrib['desc'] . '&nbsp;:</label> ';
		switch($attrib['type']) {
			case T_STR:
				echo '<input type="text" size="25" id="' . $const . '" name="' . $const . '" value="' .
				     htmlspecialchars($attrib['value']) . '" />';
				break;
			case T_INT:
			case T_FLOAT:
				echo '<input type="text" size="5" id="' . $const . '" name="' . $const . '" value="' .
				     htmlspecialchars($attrib['value']) . '" />';
			break;
			case T_BOOL:
				echo '<input type="hidden" value="1" name="is[' . $const .']" id="is[' . $const . ']" />';
				echo '<input type="checkbox" value="1" id="ch[' . $const . ']" name="ch[' . $const . ']" ' .
				     (($attrib['value']) ? 'checked="checked" ' : '') . '/>';
			break;
		}
		if($attrib['help'] != '')
			echo ' (<i>' . $attrib['help'] . '</i>)';
		echo '<br />' . (strlen($attrib['desc']) > 30 ? '<br />' : '');
	}
	echo '<a href="#">Remonter</a></fieldset>';
}
?>
	<br />
	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>
</form>