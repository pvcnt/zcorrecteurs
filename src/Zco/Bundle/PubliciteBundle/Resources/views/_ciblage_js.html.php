<script type="text/javascript">
$('aucun_age_min').addEvent('change', function(){
	if (this.checked)
	{
		$('age_min').setStyle('background-color', '#efefef');
		$('age_min').set('readonly', true);
		$('age_min').set('value', '-');
	}
	else
	{
		$('age_min').setStyle('background-color', 'white');
		$('age_min').set('readonly', false);
		$('age_min').set('value', 0);
	}
});
$('aucun_age_max').addEvent('change', function(){
	if (this.checked)
	{
		$('age_max').setStyle('background-color', '#efefef');
		$('age_max').set('readonly', true);
		$('age_max').set('value', '-');
	}
	else
	{
		$('age_max').setStyle('background-color', 'white');
		$('age_max').set('readonly', false);
		$('age_max').set('value', 0);
	}
});
window.addEvent('domready', function(){
	<?php if (!isset($cibler_age_min) || !$cibler_age_min){ ?>
	$('age_min').setStyle('background-color', '#efefef');
	$('age_min').set('readonly', true);
	<?php } if (!isset($cibler_age_max) || !$cibler_age_max){ ?>
	$('age_max').setStyle('background-color', '#efefef');
	$('age_max').set('readonly', true);
	<?php } if (!isset($cibler_categories) || !$cibler_categories){ ?>
	$('row_cibler_categories').toggle('out');
	<?php } if (!isset($cibler_pays) || !$cibler_pays){ ?>
	$('row_cibler_pays').toggle('out');
	<?php } if (!isset($cibler_age) || !$cibler_age){ ?>
	$('row_cibler_age').toggle('out');
	<?php } ?>
});
</script>