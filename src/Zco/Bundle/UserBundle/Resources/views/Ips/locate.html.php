<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Géolocaliser une adresse IP</h1>

<p>
    Notez bien que les informations ne sont pas garanties totalement exactes,
    certaines adresses ou l'utilisation d'un proxy peut fausser les résultats.
</p>
<p>
    Vous tentez de localiser l'adresse IP : <em><?php echo htmlspecialchars($ip) ?></em>
    - <a href="http://dns-tools.domaintools.com/ip-tools/?method=traceroute&query=<?php echo htmlspecialchars($ip) ?>">Résoudre cette IP</a>
    <?php if(verifier('ips_bannir')){ ?>
        - <a href="<?php echo $view['router']->path('zco_user_ips_ban', ['ip' => $ip]) ?>">Bannir cette IP</a>
    <?php } ?>
</p>

<p class="center alert alert-info">
	<strong>Localisation supposée : </strong>
    <?php echo $info ?>
</p>