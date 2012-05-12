<p>
	<strong>Ceci n'est pas un «&nbsp;jeu&nbsp;» !</strong>
	Remplir le champ ci-dessous nous permet de vérifier que ce n'est pas un
	robot qui essaie de nous spammer.<br />
	Il y a quatre caractères et aucun chiffre.
	Si vous n'arrivez pas à lire l'image, cliquez dessus pour
	en obtenir une nouvelle.
</p>
<p class="centre center">
	<img src="/captcha/index-<?php echo mt_rand(0, 100); ?>.html"
		onclick="this.src='/captcha/index-'+Math.round(Math.random(0)*100)+'.html'"
		alt="Image de vérification"
		title="Cliquez pour obtenir une autre image"
	/>
</p>
