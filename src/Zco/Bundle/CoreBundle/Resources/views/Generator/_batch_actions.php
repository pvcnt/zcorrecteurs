<!--<input type="button" onclick="switch_checkbox_normal('form_batch');" value="Tout sélectionner" />-->

<select name="batch_action">
	<option value="" class="choose_action">Sélectionnez une action</option>
	<?php foreach($config['list']['batch_actions'] as $action => $params): ?>
		<?php $credentials = isset($params['credentials']) ? $params['credentials'] : (isset($config['actions'][$action]['credentials']) ? $config['actions'][$action]['credentials'] : array()) ?>
		<?php $label = isset($params['label']) ? $params['label'] : (isset($config['actions'][$action]['label']) ? $config['actions'][$action]['label'] : Util_Inflector::humanize($action)) ?>

		<?php if (verifier_array($credentials)): ?>
			<option value="<?php echo $action ?>"><?php echo $label ?></option>
		<?php endif; ?>
	<?php endforeach; ?>
</select>

<input type="submit" value="Envoyer" />