<?php

function fromEnv($name, $default) {
    return $_SERVER[$name] ?? $default;
}

// Configuration de la connexion à la base de données
$container->setParameter('database.prefix', 'zcov2_');
$container->setParameter('database.host', fromEnv('DATABASE_HOST', 'localhost'));
$container->setParameter('database.base', fromEnv('DATABASE_BASE', 'zcodev'));
$container->setParameter('database.username', fromEnv('DATABASE_USER', 'root'));
$container->setParameter('database.password', fromEnv('DATABASE_PASSWORD', ''));

// Symfony secret (utilisé par exemple pour la protection CSRF).
$container->setParameter('secret', fromEnv('SYMFONY_SECRET', 'dDj85§fd+dedS9-sE4'));

// Google Analytics.
$container->setParameter('analytics_domain', fromEnv('GA_DOMAIN', 'zcorrecteurs.fr'));
$container->setParameter('analytics_account', fromEnv('GA_ACCOUNT', ''));