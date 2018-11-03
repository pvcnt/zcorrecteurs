<?php $view->extend('::layouts/bootstrap.html.php') ?>

<div style="float: right; width: 340px;">
	<?php echo $view->render('ZcoPagesBundle:Donate:_menu.html.php', array('chequeOuVirement' => false)) ?>
</div>

<div style="margin-right: 380px;">
	<h1>Déduction fiscale des dons</h1>
	
	<p><a href="<?php echo $view['router']->path('zco_donate_index') ?>">&larr; Retour à l’accueil des dons</a></p>

	<p style="text-align: justify; margin-top: 15px;">
		Corrigraphie est une association sans but lucratif d’intérêt général. Cela 
		signifie que <strong>vous pouvez déduire une part importante de votre don 
		de vos impôts</strong>.
	</p>
	
	<p style="text-align: justify; margin-top: 15px;">
		Nos statuts entrent en effet dans le champ d’application de 
		<a href="http://www.legifrance.gouv.fr/affichCode.do?idSectionTA=LEGISCTA000006191957&cidTexte=LEGITEXT000006069577&dateTexte=20080725">l’article 200 du Code Général des Impôts</a>.
		Cela signifie qu’en tant que particulier, vous pouvez bénéficier d’une déduction 
		fiscale de 66&nbsp;% du montant de votre don, dans la limite de 20&nbsp;% du 
		revenu imposable (reportable sur cinq ans en cas de dépassement du plafond).
	</p>

	<p style="text-align: justify; margin-top: 15px;">
		Si vous êtes une entreprise, <a href="http://www.legifrance.gouv.fr/affichCodeArticle.do?idArticle=LEGIARTI000018014446&cidTexte=LEGITEXT000006069577&dateTexte=vig">l’article 238 bis du Code Général des Impôts</a>
		s’applique : vous pouvez alors bénéficier d’une déduction fiscale de 
		60&nbsp;% du montant de votre don, dans la limite de 5&nbsp;‰ du chiffre 
		d’affaires (reportable sur cinq ans en cas de dépassement du plafond).
	</p>
	
	<h2 style="margin-top: 15px;">Comment puis-je obtenir mon reçu ?</h2>
	<ul>
		<li style="text-align: justify;">
			<strong>Don en ligne ou par virement :</strong> une fois celui-ci effectué, 
			il vous suffit de <a href="<?php echo $view['router']->path('zco_about_contact', array('objet' => 'Don')) ?>">prendre contact avec nous</a>
			en pensant bien à nous donner la référence, la date et le montant du paiement, 
			vos coordonnées postales ainsi qu’une adresse courriel.
		</li>
		<li style="text-align: justify;">
			<strong>Don par chèque :</strong> joignez à votre paiement sur papier libre 
			vos coordonnées postales ainsi qu’une adresse courriel.
		</li>
	</ul>
	<p style="text-align: justify;">
		Les reçus sont normalement envoyés par courriel. Si vous préférez le recevoir 
		par courrier postal, indiquez-le-nous.
	</p>
	
	<h2 style="margin-top: 15px;">Comment bénéficier de la déduction d’impôts ?</h2>
	<p style="text-align: justify;">
		Vous devez simplement déclarer votre don sur de la déclaration d’impôts 
		correspondant à l’année du don. Cela signifie que pour un don fait aujourd’hui, 
		vous inscrirez le montant sur la déclaration que vous recevrez en <?php echo (int) date('Y') + 1 ?>.
		Vous devez déclarer le montant total du don, l’administration fiscale se chargera 
		alors de calculer le montant à déduire de vos impôts.
	</p>
	
	<h2 style="margin-top: 15px;">Je n’habite pas en France, puis-je bénéficier d’une déduction d’impôts ?</h2>
	<p style="text-align: justify;">
		Non, nous ne pouvons émettre des reçus que pour les citoyens payant leurs impôts
		en France.
	<p>
	
	<h2 style="margin-top: 15px;">J’ai encore une question…</h2>
	<p style="text-align: justify;">
		Pour toute question concernant les dons ou la déduction fiscale, n’hésitez pas 
		à <a href="<?php echo $view['router']->path('zco_about_contact', array('objet' => 'Don')) ?>">prendre contact avec nous</a>.
	</p>
</div>
