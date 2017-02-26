<?php $label = $label ?: ucfirst(str_replace('_', ' ', $name)) ?>
<?php if (isset($style) && 'checkbox' === $style): ?>
<span <?php foreach($attr as $k => $v) { printf('%s="%s" ', $view->escape($k), $view->escape($v)); } ?>><?php echo $view->escape($label) ?><?php if ($required) : ?>&nbsp;<span class="required" title="Ce champ est requis.">*</span><?php endif ?></span>
<?php else: ?>
<?php if (isset($attr['class'])) $attr['class'] .= ' control-label'; else $attr['class'] = 'control-label'; ?>
<label for="<?php echo $view->escape($id) ?>" <?php foreach($attr as $k => $v) { printf('%s="%s" ', $view->escape($k), $view->escape($v)); } ?>>
    <?php echo $view->escape($label) ?>
    <?php if ($required) : ?>&nbsp;<span class="required" title="Ce champ est requis.">*</span><?php endif ?>
</label>
<?php endif ?>