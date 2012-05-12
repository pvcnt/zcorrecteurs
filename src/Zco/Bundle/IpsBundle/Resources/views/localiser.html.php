<?php $view->extend('::layouts/default.html.php') ?>

<h1>Géolocaliser une adresse IP</h1>

<p>Notez bien que les informations ne sont pas garanties totalement exactes, certaines adresses ou l'utilisation d'un proxy peut fausser les résultats.<br />
Vous tentez de localiser l'adresse IP : <strong><?php echo $ip; ?></strong>
- <a href="analyser.html?ip=<?php echo $_GET['ip']; ?>">Analyser cette IP</a>
- <a href="http://dns-tools.domaintools.com/ip-tools/?method=traceroute&query=<?php echo $_GET['ip']; ?>">Résoudre cette IP</a>
 <?php if(verifier('ips_bannir')){ ?> - <a href="bannir.html?ip=<?php echo $_GET['ip']; ?>">Bannir cette IP</a><?php } ?>.</p>

<p class="centre gras"><a href="analyser.html">Analyser une nouvelle adresse IP</a></p>

<fieldset class="centre">
	<legend>Localisation de l'adresse IP</legend>
	<div id="note"><strong>Localisation supposée : </strong><?php echo $info; ?></div><br />

	<div id="carte" style="width: 750px; height: 450px; margin: auto;"></div>
</fieldset>


<?php $view['javelin']->initBehavior('ips-display-marker-on-map', array(
    'id' => 'carte', 
    'latitude' => $latitude, 
    'longitude' => $longitude,
)) ?>