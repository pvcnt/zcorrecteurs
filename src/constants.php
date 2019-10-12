<?php
/**
 * Constantes du site, qui ne sont pas modifiées dynamiquement.
 */

//État des billets du blog
define('BLOG_BROUILLON',           1);
define('BLOG_PROPOSE',             2);
//define('BLOG_PREPARATION',         3);
define('BLOG_VALIDE',              4);
define('BLOG_REFUSE',              5);

//Types de commentaires sur un billet du blog
define('COMMENTAIRES_NONE',        0);
define('COMMENTAIRES_TOPIC',       1);
define('COMMENTAIRES_OK',          2);

//Versionnage
define('VERSION_CURRENT',          1); //Permet de récupérer la version courante
define('VERSION_BROUILLON',        2); //Permet de récupérer la version en brouillon
define('VERSION_ID',               3); //Permet de récupérer une version par son id

// États des dictées
define('DICTEE_BROUILLON',         1);
// 2 was used for submitted dictations.
define('DICTEE_VALIDEE',           3);

//Sexe
define('SEXE_MASCULIN', 1);
define('SEXE_FEMININ', 2);

// Configuration.
const URL_SITE = 'http://www.zcorrecteurs.fr';