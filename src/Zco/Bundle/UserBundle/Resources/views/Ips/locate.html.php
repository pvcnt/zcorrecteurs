<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Géolocaliser une adresse IP</h1>

<p>
    Notez bien que les informations ne sont pas garanties totalement exactes, certaines adresses ou
    l'utilisation d'un proxy peut fausser les résultats.<br />
    Vous tentez de localiser l'adresse IP : <strong><?php echo htmlspecialchars($ip) ?></strong>
    - <a href="<?php echo $view['router']->path('zco_user_ips_analyze', ['ip' => $ip]) ?>">Analyser cette IP</a>
    - <a href="http://dns-tools.domaintools.com/ip-tools/?method=traceroute&query=<?php echo htmlspecialchars($ip) ?>">Résoudre cette IP</a>
    <?php if(verifier('ips_bannir')){ ?>
        - <a href="<?php echo $view['router']->path('zco_user_ips_ban', ['ip' => $ip]) ?>">Bannir cette IP</a>
    <?php } ?>.
</p>

<p class="bold center">
    <a href="<?php echo $view['router']->path('zco_user_ips_analyze') ?>">Analyser une nouvelle adresse IP</a>
</p>

<p class="center">
	<div id="note"><strong>Localisation supposée : </strong><?php echo $info ?></div>
</p>