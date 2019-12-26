<?php

// Configuration de la connexion à la base de données
$container->setParameter('database.prefix', 'zcov2_');
$container->setParameter('database.host', getenv('DATABASE_HOST') ?: 'localhost');
$container->setParameter('database.base', getenv('DATABASE_BASE') ?: 'zcodev');
$container->setParameter('database.username', getenv('DATABASE_USER') ?: 'zcodev');
$container->setParameter('database.password', getenv('DATABASE_PASSWORD') ?: 'pass');

// Symfony secret (utilisé par exemple pour la protection CSRF).
$container->setParameter('secret', getenv('SYMFONY_SECRET') ?: 'dDj85§fd+dedS9-sE4');

// Google Analytics.
$container->setParameter('analytics_domain', getenv('GA_DOMAIN') ?: 'zcorrecteurs.fr');
$container->setParameter('analytics_account', getenv('GA_ACCOUNT') ?: '');