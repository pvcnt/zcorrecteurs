<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoCoreBundle:Static:legalTabs.html.php', array('currentTab' => 'mentions')) ?>

<h1>Mentions légales</h1>

<p class="good">
	Ce site web est géré par Corrigraphie, association française de loi 1901 
	déclarée à la préfecture de Lyon :
</p>

<address style="border-left: 5px solid #eee; padding-left: 10px;">
	<b>Corrigraphie — c/o CCO</b><br />
	39 rue Georges Courteline<br />
	69100 Villeurbanne<br />
	France<br />
	<b>Courriel :</b> 
	<a href="mailto:association@corrigraphie.org">association@corrigraphie.org</a><br />
	<b>SIRET :</b> 532 778 917 00023<br />
	<b>Code APE :</b> 9499Z
</address>

<p class="good">
	Le site est hébergé sur un serveur dédié loué chez OVH dont le siège 
	social est au 2 rue Kellermann, 59100 Roubaix, France.
</p>