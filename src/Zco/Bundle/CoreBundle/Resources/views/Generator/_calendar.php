<?php
$months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
$current_month = !empty($_GET[$date_hierarchy.'__month']) ? $_GET[$date_hierarchy.'__month'] : date('m');
$current_year = !empty($_GET[$date_hierarchy.'__year']) ? $_GET[$date_hierarchy.'__year'] : date('Y');

$previous_month = $current_month > 1 ? $current_month-1 : 12;
$next_month = $current_month < 12 ? $current_month+1 : 1;
$previous_year = $current_month > 1 ? $current_year : $current_year-1;
$next_year = $current_month < 12 ? $current_year : $current_year+1;
?>

<form method="get" action="" id="form_date_hierarchy">
	<a href="?<?php echo $date_hierarchy; ?>__month=<?php echo $previous_month; ?>&<?php echo $date_hierarchy; ?>__year=<?php echo $previous_year; ?>">«</a>

	<select name="<?php echo $date_hierarchy; ?>__month" id="month" onchange="$('form_date_hierarchy').submit();">
		<option value=""<?php if(isset($_GET['all'])) echo ' selected="selected"'; ?> class="choose_action">Tous</option>
		<?php foreach($months as $i => $month){ ?>
		<option value="<?php echo $i+1; ?>"<?php if(!isset($_GET['all']) && $current_month == $i+1) echo ' selected="selected"'; ?>>
			<?php echo $month; ?>
		</option>
		<?php } ?>
	</select>

	<select name="<?php echo $date_hierarchy; ?>__year" id="year" onchange="$('form_date_hierarchy').submit();">
		<option value=""<?php if(isset($_GET['all'])) echo ' selected="selected"'; ?> class="choose_action">Toutes</option>
		<?php for($i = 2007 ; $i <= date('Y') ; $i++){ ?>
		<option value="<?php echo $i; ?>"<?php if(!isset($_GET['all']) && $current_year == $i) echo ' selected="selected"'; ?>>
			<?php echo $i; ?>
		</option>
		<?php } ?>
	</select>

	<noscript><input type="submit" value="Aller" /></noscript>

	<a href="?<?php echo $date_hierarchy; ?>__month=<?php echo $next_month; ?>&<?php echo $date_hierarchy; ?>__year=<?php echo $next_year; ?>">»</a>
</form>
