<?php
/**
 * Constantes du site, qui ne sont pas modifiées dynamiquement.
 */

//Compte automatique
define('ID_COMPTE_AUTO',           2);
define('PSEUDO_COMPTE_AUTO',       'zGardien');

//Questions de sondages
define('QUESTION_SIMPLE',          1);
define('QUESTION_MULTIPLE',        2);

// Types de tutos
define('MINI_TUTO',                1);
define('MINITUTO',                 1);
define('MINI     ',                1);
define('BIG_TUTO',                 2);
define('BIGTUTO',                  2);
define('BIG',                      2);
define('TUTO_PRIORITAIRE',         3);
define('PRIORITAIRE',              3);

//États des corrections de tutos et de textes
define('ENVOYE',                   1);
define('REFUSE',                   2);
define('CORRECTION',               3);
define('CORRECTION_COMMENCEE',     4);
define('CORRECTION_ABANDONNEE',    5);
define('CORRECTION_RETIREE',       17);
define('RECORRECTION_DEMANDEE',    6);
define('RECORRECTION',             7);
define('RECORRECTION_COMMENCEE',   8);
define('RECORRECTION_ABANDONNEE',  9);
define('TERMINE_CORRIGE',          10);
define('TERMINE_RECORRIGE',        11);

//États des soumissions de textes
define('ATTENTE_CORRECTEUR',       12);
define('ATTENTE_RECORRECTEUR',     13);
define('RELECTURE',                14);
define('REVIEW',                   15);
define('REVIEW_REFUSE',            17);
define('TERMINE',                  16);

//États des fins de correction de textes
define('FIN_CORR_AUTO',            1);
define('FIN_CORR_VALID',           2);
define('FIN_CORR_REVIEW',          3);
define('FIN_CORR_RECORRECTION',    4);
define('FIN_CORR_RELECTURE',       5);

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

//États des recrutements
define('RECRUTEMENT_OUVERT',       1);
define('RECRUTEMENT_CACHE',        2);
define('RECRUTEMENT_FINI',         4);

//Types de tests
define('TEST_TEXTE',               1);
define('TEST_TUTO',                2);
define('TEST_DEFAUT',              3);

//États des candidatures
define('CANDIDATURE_REDACTION',    1);
define('CANDIDATURE_ENVOYE',       2);
define('CANDIDATURE_ATTENTE_TEST', 3);
define('CANDIDATURE_TESTE',        4);
define('CANDIDATURE_ACCEPTE',      5);
define('CANDIDATURE_REFUSE',       6);
define('CANDIDATURE_DESISTE',      7);

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

//États des projets
define('PROJET_ENVOYE',            1);
define('PROJET_ACCEPTE',           2);
define('PROJET_REFUSE',            3);
define('PROJET_MAJ',               4);

//Versionnage
define('VERSION_CURRENT',          1); //Permet de récupérer la version courante
define('VERSION_BROUILLON',        2); //Permet de récupérer la version en brouillon
define('VERSION_ID',               3); //Permet de récupérer une version par son id

//Langages
define('TEXTE',                    1);
define('ZCODE',                    2);
define('BBCODE',                   3);
define('MDOWN',                    4);
define('HTML',                     5);

//Types de MAP
define('MAP_FIRST',                1);
define('MAP_ALL',                  2);

//Ressources
define('RESSOURCES_FICHES_HORSLIGNE', 0);
define('RESSOURCES_FICHES_ENLIGNE',   1);

//Encarts publicitaires
define('PUB_ENTETE',               1);
define('PUB_MENU',                 2);
define('PUB_FOOTER',               3);

//Types de paiements
define('PAIEMENT_PAYPAL',          1);

//Types de logos
define('LOGO_BOUTON',              1);
define('LOGO_BANNIERE',            2);

// États des dictées
define('DICTEE_BROUILLON',         1);
define('DICTEE_PROPOSEE',          2);
define('DICTEE_VALIDEE',           3);

// Pour l'admin
define('ADMIN_SEP',                2);

//Pour les sélections, peut servir partout
define('ALL',                      100);

//Sexe
define('SEXE_MASCULIN', 1);
define('SEXE_FEMININ', 2);
