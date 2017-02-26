<?php if ($widget == 'single_text'): ?>
    <?php echo $view['form']->block($form, 'form_widget_simple'); ?>
	<?php $view['javelin']->initBehavior('datepicker', array(
	        'id' => $id,
	        'options' => array(
			    'pickerClass' => 'datepicker_vista',
    			'days' => array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'),
    			'months' => array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'),
    			'timePicker' => false,
    			'format' => 'd/m/Y',
    			'inputOutputFormat' => 'Y-m-d',
    			'allowEmpty' => !$required,
    		),
		)
	) ?>
<?php else: ?>
    <div <?php echo $view['form']->block($form, 'container_attributes') ?>>
        <?php echo str_replace(array('{{ year }}', '{{ month }}', '{{ day }}'), array(
            $view['form']->widget($form['year']),
            $view['form']->widget($form['month']),
            $view['form']->widget($form['day']),
        ), $date_pattern) ?>
    </div>
<?php endif ?>
