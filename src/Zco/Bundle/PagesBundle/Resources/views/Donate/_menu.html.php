<?php if (!isset($donner) || $donner): ?>
<div style="border: solid #BBBBBB 1px; background-color: #EFEFFF; padding-left: 10px; padding-right: 10px;">
    <h1>Donner<?php if (!$chequeOuVirement): ?> en ligne<?php endif; ?></h1>

    <p class="gras" style="text-align: justify;">
        Chaque année, vos dons nous aident à poursuivre notre mission et développer de nouveaux
        services.
    </p>

    <?php if (!$chequeOuVirement): ?>
        <form method="post" action="https://www.paypal.com/cgi-bin/webscr" id="formPaypal">
            <input type="hidden" name="business" value="9EKFDPDVR4JKJ" />
            <input type="hidden" name="cmd" value="_donations" />
            <input type="hidden" name="return" value="<?php echo $view['router']->url('zco_donate_thanks') ?> />
            <input type="hidden" name="item_name" value="Don" />
            <input type="hidden" name="item_number" value="Don via zCorrecteurs.fr" />
            <input type="hidden" name="currency_code" value="EUR" />
            <input type="hidden" name="amount" id="amount" value="" />

            <p>Je soutiens l’association Corrigraphie en donnant&nbsp;:</p>

            <div style="width: 280px; margin: auto;">
                <label class="nofloat"onclick="$('amount').set('value', 5); $('montant_libre').set('value', '');"><input type="radio" name="montant" value="5" />&nbsp;5&nbsp;€</label>
                <label class="nofloat" style="margin-left: 27px;" onclick="$('amount').set('value', 10); $('montant_libre').set('value', '');"><input type="radio" name="montant" value="10" />&nbsp;10&nbsp;€</label>
                <label class="nofloat" style="margin-left: 20px;"onclick="$('amount').set('value', 15); $('montant_libre').set('value', '');"><input type="radio" name="montant" value="15" />&nbsp;15&nbsp;€</label><br />

                <label class="nofloat"onclick="$('amount').set('value', 30); $('montant_libre').set('value', '');"><input type="radio" name="montant" value="30" />&nbsp;30&nbsp;€</label>
                <label class="nofloat" style="margin-left: 20px;"onclick="$('amount').set('value', 50); $('montant_libre').set('value', '');"><input type="radio" name="montant" value="50" />&nbsp;50&nbsp;€</label>
                <label class="nofloat" style="margin-left: 20px;" onclick="$('montant_libre').focus();"><input type="radio" name="montant" id="montant_autre" value="autre" />&nbsp;Autre&nbsp;:</label>
                <input type="text" name="montant_libre" id="montant_libre" class="span1" onclick="$('montant_autre').set('checked', true)" onchange="$('amount').set('value', this.get('value').replace(',', '.').replace(' ', ''));" />&nbsp;€<br />

                <input type="submit" class="btn btn-primary" value="Donner &rarr;" style="margin-top: 5px;" />
            </div>
        </form>

        <p style="margin-top: 20px; text-align: justify; background-color: white; border-top: 1px solid #DDD; border-bottom: 1px solid #DDD; padding: 5px;">
            <img src="/bundles/zcoforum/img/resolu.png" alt="" />
            Votre don ouvre droit à <a href="<?php echo $view['router']->path('zco_donate_fiscalDeduction') ?>">une déduction fiscale</a>.<br />

            <img src="/bundles/zcoforum/img/cadenas.png" alt="" />
            Paiement sécurisé par carte bancaire <em>via</em> PayPal.
        </p>

        <p style="margin-top: 20px; text-align: justify;">
            Vous pouvez aussi faire un <a href="<?php echo $view['router']->path('zco_donate_otherWays') ?>">don par chèque ou virement</a>.
        </p>
    <?php else: ?>
        <p style="margin-top: 20px; text-align: justify; background-color: white; border-top: 1px solid #DDD; border-bottom: 1px solid #DDD; padding: 5px;">
            <img src="/bundles/zcoforum/img/resolu.png" alt="" />
            Votre don ouvre droit à <a href="<?php echo $view['router']->path('zco_donate_fiscalDeduction') ?>">une déduction fiscale</a>.
        </p>

        <p style="margin-top: 20px; text-align: justify;">
            Vous pouvez aussi faire un <a href="<?php echo $view['router']->path('zco_donate_index') ?>">don en ligne</a>.
        </p>
    <?php endif; ?>

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