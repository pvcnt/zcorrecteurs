<?php if (!isset($donner) || $donner): ?>
<div style="border: solid #BBBBBB 1px; background-color: #EFEFFF; padding-left: 10px; padding-right: 10px;">
    <h1>Donner<?php if (!$chequeOuVirement): ?> en ligne<?php endif; ?></h1>

    <p class="gras" style="text-align: justify;">
        Chaque année, vos dons nous aident à poursuivre notre mission et développer de nouveaux
        services.
    </p>

    <?php echo $this->render('ZcoCoreBundle:Donate:_form.html.php', array('chequeOuVirement' => $chequeOuVirement)) ?>

    <p style="text-align: justify;">
        Pour avoir plus d’informations <a href="<?php echo $view['router']->path('zco_about_contact', array('objet' => 'Don')) ?>">sur les dons</a>
        ou <a href="<?php echo $view['router']->path('zco_about_contact', array('objet' => 'Association')) ?>">sur notre association</a>, n’hésitez
        pas à prendre contact avec nous</a>.
    </p>
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