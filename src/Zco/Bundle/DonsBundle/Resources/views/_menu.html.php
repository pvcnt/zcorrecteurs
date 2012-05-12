<?php if (!isset($donner) || $donner): ?>
<div style="border: solid #BBBBBB 1px; background-color: #EFEFFF; padding-left: 10px; padding-right: 10px;">
    <h1>Donner<?php if (!$chequeOuVirement): ?> en ligne<?php endif; ?></h1>
	
	<?php echo $this->render('ZcoDonsBundle::_formulaireDon.html.php', compact('chequeOuVirement')) ?>
</div>
<?php endif; ?>

<div style="text-align: center; border: 1px solid #DDD; background-color: whiteSmoke; padding: 5px; margin-top: 10px; margin-bottom: 10px;">
    <h4>Comment est dépensé l’argent des dons ?</h4>
    <div id="graph_depenses">
        <img src="/img/ajax-loader.gif" alt="Chargement…" style="margin-bottom: 10px;" />
    </div>

	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript">google.load("visualization", "1", {packages:["corechart"]});</script>
    <?php $view['javelin']->initBehavior('dons-pie', array('id' => 'graph_depenses')) ?>
</div>