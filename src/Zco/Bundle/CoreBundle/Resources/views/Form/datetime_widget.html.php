<?php if ($widget == 'single_text'): ?>
    <?php echo $view['form']->renderBlock('field_widget'); ?>
	<?php $view['javelin']->initBehavior('datepicker', array(
	        'id' => $id,
	        'options' => array(
			    'pickerClass' => 'datepicker_vista',
    			'days' => array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'),
    			'months' => array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'),
    			'timePicker' => true,
    			'format' => 'd/m/Y H:i:s',
    			'inputOutputFormat' => 'Y-m-d H:i:s',
    			'allowEmpty' => !$required,
    		),
		)
	) ?>
<?php else: ?>
    <div <?php echo $view['form']->renderBlock('container_attributes') ?>>
        <?php echo $view['form']->widget($form['date']).' '.$view['form']->widget($form['time']) ?>
    </div>
<?php endif ?>
