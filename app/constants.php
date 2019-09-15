<?php
/**
 * Constantes du site, qui ne sont pas modifiées dynamiquement.
 */

//État des billets du blog
define('BLOG_BROUILLON',           1);
define('BLOG_PROPOSE',             2);
define('BLOG_PREPARATION',         3);
define('BLOG_VALIDE',              4);
define('BLOG_REFUSE',              5);

//Décisions de validation
define('DECISION_VALIDER',         1);
define('DECISION_REFUSER',         2);
define('DECISION_NONE',            3);
define('DECISION_FERMER',          4);
define('DECISION_CORBEILLE',       5);

//Types de commentaires sur un billet du blog
define('COMMENTAIRES_NONE',        0);
define('COMMENTAIRES_TOPIC',       1);
define('COMMENTAIRES_OK',          2);

//États des changements de pseudos
define('CH_PSEUDO_ACCEPTE',        1);
define('CH_PSEUDO_REFUSE',         2);
define('CH_PSEUDO_ATTENTE',        3);
define('CH_PSEUDO_AUTO',           4);

//Status des participants des MP
define('MP_STATUT_SUPPRIME',      -1); //Participant au MP qui s'est supprimé.
define('MP_STATUT_NORMAL',         0); //Juste un participant au MP.
define('MP_STATUT_MASTER',         1); //Maître de conversation. Il peut ajouter des participants ou en retirer.
define('MP_STATUT_OWNER',          2); //Créateur du MP, il peut aussi bien ajouter des participants que des maîtres de conversation.

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
const TEMPS_BILLET_HASARD = 30; // minutes
const URL_SITE = 'http://www.zcorrecteurs.fr';
const ID_COMPTE_AUTO = 2;
const PSEUDO_COMPTE_AUTO = 'zGardien';
const PM_MAX_PARTICIPANTS = 20;