<?php $view->extend('ZcoDonsBundle::layout.html.php') ?> 

<div style="float: right; width: 340px;">
    <?php echo $view->render('ZcoDonsBundle::_menu.html.php', array('chequeOuVirement' => true)) ?>
</div>

<div style="margin-right: 370px;">
    <h1>Faire un don par chèque ou virement</h1>

    <p><a href="/dons/">&larr; Retour à l’accueil des dons</a></p>

    <p style="text-align: justify; margin-top: 15px;">
        Si vous ne pouvez ou ne voulez pas faire un don en ligne mais souhaitez tout de
        même soutenir notre action, vous pouvez donner par chèque ou virement bancaire.
        Dans les deux cas, pensez à joindre votre adresse électronique si vous souhaitez 
        recevoir des informations sur l’actualité de l’association et ses activités.
    </p>

    <h2 style="margin-top: 15px;">Don par chèque</h2>
    <p style="text-align: justify;">
        Vous pouvez faire un don en adressant un chèque à l’ordre de l’«&nbsp;Association Corrigraphie&nbsp;»
        au siège social de l’association :
    </p>

    <p style="margin-left: 30px;">
        Corrigraphie — c/o CCO<br />
        39 rue Georges Courteline<br />
        69100 Villeurbanne<br />
        France
    </p>

	<p style="text-align: justify;">
		Si vous souhaitez recevoir un reçu afin de bénéficier d’<a href="deduction-fiscale.html">une déduction fiscale</a>, 
		pensez à joindre à votre chèque votre nom, votre adresse postale ainsi qu’une adresse 
		courriel à laquelle nous pourrons envoyer le reçu (si vous en avez une).
	</p>

    <h2 style="margin-top: 15px;">Don par virement</h2>
    <p style="text-align: justify;">
        Vous pouvez également faire un don par virement en utilisant les coordonnées 
        bancaires suivantes :
    </p>

    <ul>
        <li><strong>Bénéficiaire :</strong> Association Corrigraphie</li>
        <li><strong>Banque :</strong> Crédit Agricole (Centre Loire)</li>
        <li><strong>RIB :</strong> 14806 18000 70082088828 26</li>
        <li><strong>IBAN :</strong> FR76 1480 6180 0070 0820 8882 826</li>
        <li><strong>BIC :</strong> AGRIFRPP848</li>
    </ul>

	<p style="text-align: justify;">
		Si vous souhaitez recevoir un reçu afin de bénéficier d’<a href="deduction-fiscale.html">une déduction fiscale</a>, 
		pensez à <a href="<?php echo $view['router']->generate('zco_about_contact', array('objet' => 'Don')) ?>">prendre contact avec nous</a> en mentionnant le 
		numéro de la transaction, votre nom, votre adresse postale ainsi qu’une adresse courriel 
		à laquelle nous pourrons envoyer le reçu (si vous en avez une).
	</p>
</div>