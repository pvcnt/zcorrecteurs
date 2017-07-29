CREATE TABLE `zcov2_aide` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `categorie_id` bigint(20) NOT NULL,
  `titre` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `contenu` text COLLATE utf8_unicode_ci,
  `racine` tinyint(1) DEFAULT '1',
  `date` datetime NOT NULL,
  `date_edition` datetime NOT NULL,
  `icone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ordre` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `zcov2_annonces` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` bigint(20) DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `poids` bigint(20) DEFAULT '100',
  `contenu` text COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `nb_clics` int(11) NOT NULL DEFAULT '0',
  `nb_affichages` int(11) NOT NULL DEFAULT '0',
  `nb_fermetures` int(11) NOT NULL DEFAULT '0',
  `aff_pays_inconnu` tinyint(1) NOT NULL,
  `url_destination` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `zcov2_annonces_categories` (
  `annonce_id` bigint(20) NOT NULL DEFAULT '0',
  `categorie_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`annonce_id`,`categorie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_annonces_domaines` (
  `annonce_id` bigint(20) NOT NULL DEFAULT '0',
  `domaine` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`annonce_id`,`domaine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_annonces_groupes` (
  `annonce_id` bigint(20) NOT NULL DEFAULT '0',
  `groupe_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`annonce_id`,`groupe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_annonces_pays` (
  `annonce_id` bigint(20) NOT NULL DEFAULT '0',
  `pays_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`annonce_id`,`pays_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_auteurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `autres` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `utilisateur_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_avertissements` (
  `averto_id` int(11) NOT NULL AUTO_INCREMENT,
  `averto_id_utilisateur` int(11) NOT NULL,
  `averto_id_admin` int(11) NOT NULL,
  `averto_pourcentage` tinyint(3) NOT NULL,
  `averto_litige` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `averto_date` datetime NOT NULL,
  `averto_raison` text COLLATE utf8_unicode_ci NOT NULL,
  `averto_raison_admin` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`averto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_bannieres` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `chemin` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `largeur` int(11) DEFAULT NULL,
  `hauteur` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_blog` (
  `blog_id` int(11) NOT NULL AUTO_INCREMENT,
  `blog_id_categorie` int(11) NOT NULL,
  `blog_id_ressource` int(11) DEFAULT NULL,
  `blog_date` datetime NOT NULL,
  `blog_date_edition` datetime NOT NULL,
  `blog_date_validation` datetime NOT NULL,
  `blog_date_proposition` datetime NOT NULL,
  `blog_date_publication` datetime NOT NULL,
  `blog_etat` tinyint(4) NOT NULL,
  `blog_lien_topic` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `blog_commentaires` tinyint(1) NOT NULL,
  `blog_id_version_courante` int(11) NOT NULL,
  `blog_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `blog_url_redirection` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `blog_lien_nom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `blog_lien_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `blog_nb_vues` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`blog_id`),
  KEY `blog_id_version_courante` (`blog_id_version_courante`),
  KEY `zcov2_blog_ibfk_1` (`blog_id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_blog_auteurs` (
  `auteur_id_utilisateur` int(11) NOT NULL,
  `auteur_id_billet` int(11) NOT NULL,
  `auteur_statut` tinyint(1) NOT NULL,
  `auteur_date` datetime NOT NULL,
  PRIMARY KEY (`auteur_id_utilisateur`,`auteur_id_billet`),
  KEY `zcov2_blog_auteurs_ibfk_2` (`auteur_id_billet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_blog_commentaires` (
  `commentaire_id` int(11) NOT NULL AUTO_INCREMENT,
  `commentaire_id_utilisateur` int(11) DEFAULT NULL,
  `commentaire_id_billet` int(11) NOT NULL,
  `commentaire_texte` text COLLATE utf8_unicode_ci NOT NULL,
  `commentaire_date` datetime NOT NULL,
  `commentaire_edite_date` datetime NOT NULL,
  `commentaire_id_edite` int(11) NOT NULL,
  `commentaire_ip` int(11) NOT NULL,
  PRIMARY KEY (`commentaire_id`),
  KEY `zcov2_blog_commentaires_ibfk_2` (`commentaire_id_billet`),
  KEY `zcov2_blog_commentaires_ibfk_1` (`commentaire_id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_blog_flux_visites` (
  `visite_date` date NOT NULL,
  `visite_ip` int(11) NOT NULL,
  `visite_id_categorie` int(11) NOT NULL,
  `visite_user_agent` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `visite_nb_vues` int(11) NOT NULL,
  PRIMARY KEY (`visite_date`,`visite_ip`,`visite_id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_blog_lunonlu` (
  `lunonlu_id_utilisateur` int(11) NOT NULL,
  `lunonlu_id_billet` int(11) NOT NULL,
  `lunonlu_id_commentaire` int(11) NOT NULL,
  PRIMARY KEY (`lunonlu_id_utilisateur`,`lunonlu_id_billet`),
  KEY `zcov2_blog_lunonlu_ibfk_3` (`lunonlu_id_commentaire`),
  KEY `zcov2_blog_lunonlu_ibfk_2` (`lunonlu_id_billet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_blog_tags` (
  `id_tag` int(11) NOT NULL,
  `id_blog` int(11) NOT NULL,
  PRIMARY KEY (`id_tag`,`id_blog`),
  KEY `id_blog` (`id_blog`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_blog_validation` (
  `valid_id` int(11) NOT NULL AUTO_INCREMENT,
  `valid_id_billet` int(11) NOT NULL,
  `valid_id_version` int(11) DEFAULT NULL,
  `valid_id_utilisateur` int(11) DEFAULT NULL,
  `valid_date` datetime NOT NULL,
  `valid_ip` int(11) NOT NULL,
  `valid_commentaire` text COLLATE utf8_unicode_ci NOT NULL,
  `valid_decision` tinyint(4) NOT NULL,
  PRIMARY KEY (`valid_id`),
  KEY `valid_id_billet` (`valid_id_billet`,`valid_id_version`,`valid_id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_blog_versions` (
  `version_id` int(11) NOT NULL AUTO_INCREMENT,
  `version_id_billet` int(11) DEFAULT NULL,
  `version_id_utilisateur` int(11) DEFAULT NULL,
  `version_id_fictif` int(11) NOT NULL,
  `version_date` datetime NOT NULL,
  `version_ip` int(11) NOT NULL,
  `version_titre` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `version_sous_titre` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `version_texte` text COLLATE utf8_unicode_ci NOT NULL,
  `version_intro` text COLLATE utf8_unicode_ci NOT NULL,
  `version_commentaire` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`version_id`),
  KEY `version_id_billet` (`version_id_billet`,`version_id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_categories` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_nom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `cat_gauche` int(11) NOT NULL,
  `cat_droite` int(11) NOT NULL,
  `cat_niveau` int(11) NOT NULL,
  `cat_description` text COLLATE utf8_unicode_ci NOT NULL,
  `cat_description_meta` text COLLATE utf8_unicode_ci NOT NULL,
  `cat_keywords` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cat_url` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `cat_nb_elements` int(11) NOT NULL,
  `cat_last_element` int(11) NOT NULL,
  `cat_reglement` text COLLATE utf8_unicode_ci NOT NULL,
  `cat_map` text COLLATE utf8_unicode_ci NOT NULL,
  `cat_map_type` tinyint(4) NOT NULL,
  `cat_image` tinyint(1) NOT NULL,
  `cat_redirection` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cat_disponible_ciblage` tinyint(1) NOT NULL DEFAULT '1',
  `cat_ciblage_actions` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_changements_pseudos` (
  `changement_id` int(11) NOT NULL AUTO_INCREMENT,
  `changement_id_utilisateur` int(11) NOT NULL,
  `changement_id_admin` int(11) NOT NULL,
  `changement_ancien_pseudo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `changement_nouveau_pseudo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `changement_date` datetime NOT NULL,
  `changement_date_reponse` datetime NOT NULL,
  `changement_raison` text COLLATE utf8_unicode_ci NOT NULL,
  `changement_reponse` text COLLATE utf8_unicode_ci NOT NULL,
  `changement_etat` int(11) NOT NULL,
  PRIMARY KEY (`changement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_citations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `auteur_prenom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `auteur_nom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `auteur_autres` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contenu` text COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `statut` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_citations_tags` (
  `citation_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`citation_id`,`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_connectes` (
  `connecte_ip` int(11) NOT NULL,
  `connecte_id_utilisateur` int(11) NOT NULL,
  `connecte_debut` datetime NOT NULL,
  `connecte_derniere_action` datetime NOT NULL,
  `connecte_id_categorie` int(11) DEFAULT NULL,
  `connecte_id1` int(11) NOT NULL,
  `connecte_id2` int(11) NOT NULL,
  `connecte_nom_module` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `connecte_nom_action` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `connecte_user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`connecte_id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_dictees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) DEFAULT NULL,
  `auteur_id` int(11) DEFAULT NULL,
  `etat` tinyint(1) NOT NULL DEFAULT '1',
  `difficulte` tinyint(1) NOT NULL DEFAULT '1',
  `participations` int(11) NOT NULL,
  `note` tinyint(2) NOT NULL,
  `temps_estime` tinyint(2) NOT NULL,
  `titre` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `source` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creation` datetime NOT NULL,
  `edition` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `validation` datetime DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `texte` text COLLATE utf8_unicode_ci NOT NULL,
  `indications` text COLLATE utf8_unicode_ci NOT NULL,
  `commentaires` text COLLATE utf8_unicode_ci,
  `format` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `icone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dictee_etat` (`etat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_dictees_participations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dictee_id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `fautes` tinyint(3) NOT NULL,
  `note` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `participation_dictee` (`dictee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_dictees_tags` (
  `dictee_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`dictee_id`,`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_dons` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_droits` (
  `droit_id` int(11) NOT NULL AUTO_INCREMENT,
  `droit_id_categorie` int(11) NOT NULL,
  `droit_nom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `droit_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `droit_choix_categorie` tinyint(1) NOT NULL,
  `droit_choix_binaire` tinyint(1) NOT NULL,
  `droit_description_longue` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`droit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_forum_alertes` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `sujet_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `raison` text COLLATE utf8_unicode_ci NOT NULL,
  `resolu` mediumint(9) NOT NULL DEFAULT '0',
  `ip` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_forum_lunonlu` (
  `lunonlu_utilisateur_id` int(10) unsigned NOT NULL,
  `lunonlu_sujet_id` int(10) unsigned NOT NULL,
  `lunonlu_message_id` int(10) unsigned NOT NULL,
  `lunonlu_participe` tinyint(1) NOT NULL,
  `lunonlu_favori` tinyint(1) NOT NULL,
  PRIMARY KEY (`lunonlu_utilisateur_id`,`lunonlu_sujet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_forum_messages` (
  `message_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `message_auteur` int(11) unsigned NOT NULL,
  `message_texte` text COLLATE utf8_unicode_ci NOT NULL,
  `message_date` datetime NOT NULL,
  `message_sujet_id` int(11) unsigned NOT NULL,
  `message_edite_auteur` int(11) unsigned NOT NULL,
  `message_edite_date` datetime NOT NULL,
  `message_ip` int(11) NOT NULL,
  `message_help` tinyint(1) NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `message_auteur` (`message_auteur`),
  KEY `message_sujet_id` (`message_sujet_id`),
  KEY `message_edite_date` (`message_edite_date`),
  FULLTEXT KEY `message_texte` (`message_texte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_forum_messages_autos` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tag` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `texte` text COLLATE utf8_unicode_ci NOT NULL,
  `ferme` tinyint(1) unsigned NOT NULL,
  `resolu` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_forum_ordre` (
  `utilisateur_id` int(11) NOT NULL,
  `ordre` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_forum_sondages` (
  `sondage_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sondage_question` text COLLATE utf8_unicode_ci NOT NULL,
  `sondage_ferme` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sondage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_forum_sondages_choix` (
  `choix_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `choix_sondage_id` int(11) unsigned NOT NULL,
  `choix_texte` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`choix_id`),
  KEY `choix_sondage_id` (`choix_sondage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_forum_sondages_votes` (
  `vote_membre_id` int(11) unsigned NOT NULL,
  `vote_sondage_id` int(11) unsigned NOT NULL,
  `vote_choix` int(11) unsigned NOT NULL,
  `vote_date` datetime NOT NULL,
  PRIMARY KEY (`vote_membre_id`,`vote_sondage_id`),
  KEY `vote_choix` (`vote_choix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_forum_sujets` (
  `sujet_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sujet_forum_id` int(10) unsigned NOT NULL,
  `sujet_titre` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sujet_sous_titre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sujet_auteur` int(11) unsigned NOT NULL,
  `sujet_date` datetime NOT NULL,
  `sujet_premier_message` int(11) unsigned NOT NULL,
  `sujet_dernier_message` int(11) unsigned NOT NULL,
  `sujet_reponses` int(11) NOT NULL DEFAULT '0',
  `sujet_sondage` int(11) NOT NULL DEFAULT '0',
  `sujet_annonce` tinyint(1) NOT NULL DEFAULT '0',
  `sujet_ferme` tinyint(1) NOT NULL DEFAULT '0',
  `sujet_resolu` tinyint(1) NOT NULL DEFAULT '0',
  `sujet_corbeille` tinyint(1) NOT NULL DEFAULT '0',
  `sujet_coup_coeur` tinyint(1) NOT NULL,
  PRIMARY KEY (`sujet_id`),
  KEY `sujet_sondage` (`sujet_sondage`),
  KEY `sujet_dernier_message` (`sujet_dernier_message`),
  KEY `sujet_premier_message` (`sujet_premier_message`),
  KEY `sujet_auteur` (`sujet_auteur`),
  KEY `sujet_corbeille` (`sujet_corbeille`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_groupes` (
  `groupe_id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `groupe_nom` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `groupe_logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `groupe_logo_feminin` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `groupe_class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `groupe_sanction` tinyint(1) NOT NULL,
  `groupe_team` tinyint(1) NOT NULL,
  `groupe_secondaire` tinyint(1) NOT NULL,
  `groupe_description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`groupe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_groupes_droits` (
  `gd_id_droit` int(11) NOT NULL,
  `gd_id_groupe` int(11) NOT NULL,
  `gd_id_categorie` int(11) NOT NULL,
  `gd_valeur` int(11) NOT NULL,
  PRIMARY KEY (`gd_id_droit`,`gd_id_groupe`,`gd_id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_groupes_secondaires` (
  `groupe_id` int(10) unsigned NOT NULL,
  `utilisateur_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `groupe_secondaire_groupe_id` (`groupe_id`,`utilisateur_id`),
  KEY `groupe_secondaire_utilisateur_id` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_historique_groupes` (
  `chg_id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `chg_utilisateur_id` int(10) unsigned NOT NULL,
  `chg_date` datetime NOT NULL,
  `chg_responsable` int(10) unsigned NOT NULL,
  `chg_nouveau_groupe` tinyint(3) unsigned NOT NULL,
  `chg_ancien_groupe` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`chg_id`),
  KEY `chg_utilisateur_id` (`chg_utilisateur_id`,`chg_responsable`),
  KEY `chg_nouveau_groupe` (`chg_nouveau_groupe`),
  KEY `chg_ancien_groupe` (`chg_ancien_groupe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_ips_bannies` (
  `ip_id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_ip` bigint(20) NOT NULL,
  `ip_id_admin` mediumint(9) NOT NULL,
  `ip_date` datetime NOT NULL,
  `ip_duree` int(11) NOT NULL,
  `ip_duree_restante` int(11) NOT NULL,
  `ip_raison` text COLLATE utf8_unicode_ci NOT NULL,
  `ip_raison_admin` text COLLATE utf8_unicode_ci NOT NULL,
  `ip_fini` tinyint(1) NOT NULL,
  PRIMARY KEY (`ip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_licences` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `resume_url` varchar(255) DEFAULT NULL,
  `texte_url` varchar(255) DEFAULT NULL,
  `citer_auteur` tinyint(1) DEFAULT NULL,
  `conserver_licence` tinyint(1) DEFAULT NULL,
  `modif_autorisee` tinyint(1) DEFAULT NULL,
  `utilisation_commerciale` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `zcov2_livredor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `note` tinyint(1) NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `ip` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `livredor_auteur_inscrit` (`utilisateur_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_mails_bannis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) DEFAULT NULL,
  `mail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `raison` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `texte` text COLLATE utf8_unicode_ci NOT NULL,
  `date_creation` datetime NOT NULL,
  `modif_utilisateur_id` int(11) DEFAULT NULL,
  `modif_date` datetime DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `utile` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `source_id` (`source_id`,`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_messages_compteurs` (
  `source_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `nombre` int(11) NOT NULL,
  `dernier_message_id` int(11) NOT NULL,
  PRIMARY KEY (`source_id`,`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_messages_lectures` (
  `source_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  PRIMARY KEY (`source_id`,`parent_id`,`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_messages_sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_mp_alertes` (
  `mp_alerte_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mp_alerte_mp_id` int(10) unsigned NOT NULL,
  `mp_alerte_auteur` int(10) unsigned NOT NULL,
  `mp_alerte_date` datetime NOT NULL,
  `mp_alerte_raison` text COLLATE utf8_unicode_ci NOT NULL,
  `mp_alerte_ip` int(11) NOT NULL,
  `mp_alerte_resolu` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `mp_alerte_modo` int(10) unsigned NOT NULL,
  PRIMARY KEY (`mp_alerte_id`),
  KEY `mp_alerte_date` (`mp_alerte_date`,`mp_alerte_resolu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_mp_dossiers` (
  `mp_dossier_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mp_dossier_auteur_id` int(10) unsigned NOT NULL,
  `mp_dossier_titre` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`mp_dossier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_mp_messages` (
  `mp_message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mp_message_mp_id` int(10) unsigned NOT NULL,
  `mp_message_auteur_id` int(10) unsigned NOT NULL,
  `mp_message_date` datetime NOT NULL,
  `mp_message_texte` text COLLATE utf8_unicode_ci NOT NULL,
  `mp_message_ip` int(11) NOT NULL,
  PRIMARY KEY (`mp_message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_mp_mp` (
  `mp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mp_titre` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `mp_sous_titre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mp_date` datetime NOT NULL,
  `mp_premier_message_id` int(10) unsigned NOT NULL,
  `mp_dernier_message_id` int(10) unsigned NOT NULL,
  `mp_reponses` int(10) unsigned NOT NULL,
  `mp_ferme` tinyint(1) NOT NULL DEFAULT '0',
  `mp_crypte` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_mp_participants` (
  `mp_participant_mp_id` int(10) unsigned NOT NULL,
  `mp_participant_mp_dossier_id` int(11) NOT NULL DEFAULT '0',
  `mp_participant_id` int(10) unsigned NOT NULL,
  `mp_participant_statut` tinyint(1) NOT NULL DEFAULT '0',
  `mp_participant_dernier_message_lu` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`mp_participant_mp_id`,`mp_participant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_newsletter_blog` (
  `newsletter_id` int(11) NOT NULL AUTO_INCREMENT,
  `newsletter_email` varchar(130) COLLATE utf8_unicode_ci NOT NULL,
  `newsletter_hash` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `newsletter_active` tinyint(1) NOT NULL,
  `newsletter_categorie` tinyint(1) NOT NULL,
  PRIMARY KEY (`newsletter_id`),
  UNIQUE KEY `newsletter_email` (`newsletter_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_pays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_publicites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campagne_id` int(11) NOT NULL,
  `emplacement` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `nb_clics` int(11) NOT NULL,
  `nb_affichages` int(11) NOT NULL,
  `taux_clic` int(11) NOT NULL,
  `titre` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `contenu` text COLLATE utf8_unicode_ci NOT NULL,
  `contenu_js` tinyint(1) NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `approuve` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `url_cible` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `age_min` tinyint(4) DEFAULT NULL,
  `age_max` tinyint(4) DEFAULT NULL,
  `aff_age_inconnu` tinyint(1) NOT NULL DEFAULT '1',
  `aff_pays_inconnu` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `campagne_id` (`campagne_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_publicites_campagnes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `utilisateur_id` bigint(20) DEFAULT NULL,
  `etat` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nb_clics` int(11) DEFAULT NULL,
  `nb_affichages` int(11) DEFAULT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_publicites_categories` (
  `publicite_id` int(11) NOT NULL,
  `categorie_id` int(11) NOT NULL,
  `actions` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`publicite_id`,`categorie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_publicites_clics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publicite_id` int(11) NOT NULL,
  `categorie_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `age` tinyint(4) NOT NULL,
  `pays` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `ip` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `publicite_id` (`publicite_id`),
  KEY `categorie_id` (`categorie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_publicites_pays` (
  `publicite_id` int(11) NOT NULL,
  `pays_id` int(11) NOT NULL,
  PRIMARY KEY (`publicite_id`,`pays_id`),
  KEY `pays_id` (`pays_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_publicites_stats` (
  `publicite_id` int(11) NOT NULL DEFAULT '0',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `nb_affichages` int(11) DEFAULT NULL,
  `nb_clics` int(11) DEFAULT NULL,
  PRIMARY KEY (`publicite_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_push_big_tutos` (
  `big_tuto_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `big_tuto_titre` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `big_tuto_avancement` tinyint(3) unsigned NOT NULL,
  `big_tuto_difficulte` tinyint(1) NOT NULL,
  `big_tuto_tps_etude` int(11) NOT NULL,
  `big_tuto_introduction` text COLLATE utf8_unicode_ci NOT NULL,
  `big_tuto_conclusion` text COLLATE utf8_unicode_ci NOT NULL,
  `big_tuto_id_sdz` int(10) unsigned NOT NULL,
  PRIMARY KEY (`big_tuto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_push_big_tutos_parties` (
  `partie_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `partie_id_big_tuto` int(10) unsigned NOT NULL,
  `partie_titre` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `partie_introduction` text COLLATE utf8_unicode_ci NOT NULL,
  `partie_conclusion` text COLLATE utf8_unicode_ci NOT NULL,
  `partie_id_sdz` int(10) unsigned NOT NULL,
  PRIMARY KEY (`partie_id`),
  KEY `mini_tutos_id` (`partie_id_big_tuto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_push_correcteurs` (
  `correcteur_id_correction` int(10) unsigned NOT NULL,
  `correcteur_id_utilisateur` int(10) unsigned NOT NULL,
  `correcteur_invisible` tinyint(1) NOT NULL,
  `correcteur_marque` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`correcteur_id_correction`,`correcteur_id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_push_corrections` (
  `correction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `correction_id_tuto_corrige` int(10) unsigned NOT NULL,
  `correction_id_correcteur` int(11) NOT NULL,
  `correction_abandonee` tinyint(1) unsigned NOT NULL,
  `correction_date_debut` datetime DEFAULT NULL,
  `correction_date_fin` datetime DEFAULT NULL,
  `correction_commentaire` text COLLATE utf8_unicode_ci NOT NULL,
  `correction_commentaire_valido` text COLLATE utf8_unicode_ci NOT NULL,
  `correction_correcteur_invisible` tinyint(1) NOT NULL DEFAULT '1',
  `correction_marque` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`correction_id`),
  KEY `id_tuto_corrige` (`correction_id_tuto_corrige`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_push_mini_tuto_sous_parties` (
  `sous_partie_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sous_partie_id_mini_tuto` int(10) unsigned NOT NULL,
  `sous_partie_titre` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `sous_partie_texte` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `sous_partie_id_sdz` int(10) unsigned NOT NULL,
  PRIMARY KEY (`sous_partie_id`),
  KEY `sous_partie_id_mini_tuto` (`sous_partie_id_mini_tuto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_push_mini_tutos` (
  `mini_tuto_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mini_tuto_id_partie` int(10) unsigned NOT NULL,
  `mini_tuto_titre` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `mini_tuto_avancement` tinyint(3) unsigned NOT NULL,
  `mini_tuto_difficulte` tinyint(1) NOT NULL,
  `mini_tuto_tps_etude` int(11) NOT NULL,
  `mini_tuto_introduction` text COLLATE utf8_unicode_ci NOT NULL,
  `mini_tuto_conclusion` text COLLATE utf8_unicode_ci NOT NULL,
  `mini_tuto_id_sdz` int(10) unsigned NOT NULL,
  PRIMARY KEY (`mini_tuto_id`),
  KEY `id_partie` (`mini_tuto_id_partie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_push_qcm_questions` (
  `question_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_id_mini_tuto` int(10) unsigned NOT NULL,
  `question_label` text COLLATE utf8_unicode_ci NOT NULL,
  `question_explications` text COLLATE utf8_unicode_ci NOT NULL,
  `question_id_sdz` int(10) unsigned NOT NULL,
  `question_ordre_sdz` int(10) unsigned NOT NULL,
  PRIMARY KEY (`question_id`),
  KEY `id_mini_tuto` (`question_id_mini_tuto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_push_qcm_reponses` (
  `reponse_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reponse_id_qcm_question` int(10) unsigned NOT NULL,
  `reponse_texte` text COLLATE utf8_unicode_ci NOT NULL,
  `reponse_vrai` tinyint(1) NOT NULL,
  `reponse_id_sdz` int(10) unsigned NOT NULL,
  PRIMARY KEY (`reponse_id`),
  KEY `id_qcm_question` (`reponse_id_qcm_question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_push_soumissions` (
  `soumission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `soumission_id_utilisateur` int(10) unsigned NOT NULL,
  `soumission_pseudo_utilisateur` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `soumission_id_valido` int(10) unsigned NOT NULL DEFAULT '0',
  `soumission_pseudo_valido` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `soumission_id_tuto_sdz` int(11) NOT NULL,
  `soumission_description` text COLLATE utf8_unicode_ci NOT NULL,
  `soumission_sauvegarde` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `soumission_date` datetime NOT NULL,
  `soumission_type_tuto` tinyint(1) unsigned NOT NULL,
  `soumission_id_tuto` int(10) unsigned NOT NULL,
  `soumission_id_correction_1` smallint(5) unsigned DEFAULT NULL,
  `soumission_id_correction_2` smallint(5) unsigned DEFAULT NULL,
  `soumission_prioritaire` tinyint(1) NOT NULL DEFAULT '0',
  `soumission_recorrection` tinyint(1) NOT NULL DEFAULT '0',
  `soumission_ip` int(11) NOT NULL,
  `soumission_commentaire` text COLLATE utf8_unicode_ci NOT NULL,
  `soumission_news` tinyint(1) NOT NULL,
  `soumission_avancement` smallint(4) NOT NULL DEFAULT '0',
  `soumission_etat` tinyint(4) NOT NULL,
  `soumission_token` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`soumission_id`),
  KEY `id_utilisateur` (`soumission_id_utilisateur`),
  KEY `id_tuto` (`soumission_id_tuto`),
  KEY `id_correction_1` (`soumission_id_correction_1`),
  KEY `id_correction_2` (`soumission_id_correction_2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_push_soumissions_refus` (
  `refus_id` int(11) NOT NULL AUTO_INCREMENT,
  `refus_id_soumission` int(11) NOT NULL,
  `refus_id_admin` int(11) NOT NULL,
  `refus_date` datetime NOT NULL,
  `refus_raison` text COLLATE utf8_unicode_ci NOT NULL,
  `refus_raison_admin` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`refus_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_quiz` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `categorie_id` smallint(6) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `difficulte` tinyint(1) NOT NULL,
  `date` datetime NOT NULL,
  `utilisateur_id` mediumint(9) NOT NULL,
  `aleatoire` tinyint(1) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_quiz_questions` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `quiz_id` mediumint(9) NOT NULL,
  `utilisateur_id` mediumint(9) NOT NULL,
  `date` datetime NOT NULL,
  `question` text COLLATE utf8_unicode_ci NOT NULL,
  `reponse1` text COLLATE utf8_unicode_ci NOT NULL,
  `reponse2` text COLLATE utf8_unicode_ci NOT NULL,
  `reponse3` text COLLATE utf8_unicode_ci NOT NULL,
  `reponse4` text COLLATE utf8_unicode_ci NOT NULL,
  `reponse_juste` tinyint(1) NOT NULL,
  `explication` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_quiz_scores` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `quiz_id` mediumint(9) NOT NULL,
  `utilisateur_id` mediumint(9) NOT NULL,
  `note` smallint(6) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_recrutements` (
  `recrutement_id` int(11) NOT NULL AUTO_INCREMENT,
  `recrutement_id_utilisateur` int(11) NOT NULL,
  `recrutement_id_quiz` int(11) DEFAULT NULL,
  `recrutement_id_groupe` int(11) NOT NULL,
  `recrutement_nom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `recrutement_date` datetime NOT NULL,
  `recrutement_date_fin_depot` datetime DEFAULT NULL,
  `recrutement_date_fin_epreuve` datetime NOT NULL,
  `recrutement_etat` tinyint(4) NOT NULL,
  `recrutement_texte` text COLLATE utf8_unicode_ci NOT NULL,
  `recrutement_prive` tinyint(1) NOT NULL,
  `recrutement_nb_personnes` tinyint(4) NOT NULL,
  `recrutement_nb_lus` int(11) NOT NULL,
  `recrutement_redaction` tinyint(1) NOT NULL,
  `recrutement_lien` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `recrutement_test` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`recrutement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_recrutements_avis` (
  `utilisateur_id` int(11) NOT NULL,
  `candidature_id` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL,
  PRIMARY KEY (`utilisateur_id`,`candidature_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_recrutements_candidatures` (
  `candidature_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `candidature_id_utilisateur` mediumint(9) NOT NULL,
  `candidature_id_recrutement` int(11) NOT NULL,
  `candidature_id_admin` int(11) NOT NULL,
  `candidature_correcteur` int(11) DEFAULT NULL,
  `candidature_pseudo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `candidature_date` datetime NOT NULL,
  `candidature_date_debut_correction` datetime NOT NULL,
  `candidature_date_correction` datetime NOT NULL,
  `candidature_date_fin_correction` datetime NOT NULL,
  `candidature_date_reponse` datetime NOT NULL,
  `candidature_texte` text COLLATE utf8_unicode_ci NOT NULL,
  `candidature_redaction` text COLLATE utf8_unicode_ci NOT NULL,
  `candidature_quiz_score` tinyint(4) DEFAULT NULL,
  `candidature_quiz_debut` datetime DEFAULT NULL,
  `candidature_quiz_fin` datetime DEFAULT NULL,
  `candidature_etat` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `candidature_test_type` tinyint(4) NOT NULL,
  `candidature_test_tuto` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `candidature_test_texte` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `candidature_correction_original` text COLLATE utf8_unicode_ci NOT NULL,
  `candidature_correction_corrige` text COLLATE utf8_unicode_ci NOT NULL,
  `candidature_correction_note` float DEFAULT NULL,
  `candidature_ip` int(11) NOT NULL,
  `candidature_commentaire` text COLLATE utf8_unicode_ci NOT NULL,
  `candidature_correcteur_note` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`candidature_id`),
  KEY `candidature_correcteur` (`candidature_correcteur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_recrutements_commentaires` (
  `commentaire_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `commentaire_candidature_id` mediumint(9) NOT NULL,
  `commentaire_utilisateur_id` mediumint(9) NOT NULL,
  `commentaire_date` datetime NOT NULL,
  `commentaire_texte` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`commentaire_id`),
  KEY `commentaire_candidature_id` (`commentaire_candidature_id`,`commentaire_utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_recrutements_lunonlu` (
  `lunonlu_utilisateur_id` int(11) unsigned NOT NULL,
  `lunonlu_candidature_id` int(11) unsigned NOT NULL,
  `lunonlu_commentaire_id` int(11) unsigned NOT NULL,
  `lunonlu_participe` tinyint(1) NOT NULL,
  PRIMARY KEY (`lunonlu_utilisateur_id`,`lunonlu_candidature_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_recrutements_quiz_reponses` (
  `utilisateur_id` int(11) NOT NULL,
  `recrutement_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `reponse_id` int(11) DEFAULT NULL,
  `justification` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`utilisateur_id`,`recrutement_id`,`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_registry` (
  `registry_key` varchar(50) CHARACTER SET utf8 NOT NULL,
  `registry_value` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`registry_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_sanctions` (
  `sanction_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `sanction_id_utilisateur` mediumint(9) NOT NULL,
  `sanction_id_admin` mediumint(9) NOT NULL,
  `sanction_id_groupe_origine` tinyint(6) NOT NULL,
  `sanction_id_groupe_sanction` tinyint(4) NOT NULL,
  `sanction_date` datetime NOT NULL,
  `sanction_duree` int(11) NOT NULL,
  `sanction_duree_restante` int(11) NOT NULL,
  `sanction_litige` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sanction_raison` text COLLATE utf8_unicode_ci NOT NULL,
  `sanction_raison_admin` text COLLATE utf8_unicode_ci NOT NULL,
  `sanction_finie` tinyint(1) NOT NULL,
  PRIMARY KEY (`sanction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_sauvegardes_zform` (
  `sauvegarde_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `sauvegarde_id_utilisateur` mediumint(9) NOT NULL,
  `sauvegarde_date` datetime NOT NULL,
  `sauvegarde_texte` text COLLATE utf8_unicode_ci NOT NULL,
  `sauvegarde_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`sauvegarde_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_sondages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(10) unsigned DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `date_debut` datetime DEFAULT NULL,
  `date_fin` datetime DEFAULT NULL,
  `nb_questions` tinyint(4) NOT NULL DEFAULT '0',
  `ouvert` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_sondages_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sondage_id` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nb_votes` int(11) NOT NULL,
  `nb_blanc` int(11) NOT NULL,
  `ordre` tinyint(4) NOT NULL,
  `libre` tinyint(1) NOT NULL DEFAULT '0',
  `nb_min_choix` int(11) NOT NULL,
  `nb_max_choix` int(11) NOT NULL,
  `obligatoire` tinyint(1) NOT NULL DEFAULT '0',
  `resultats_publics` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sondage_id` (`sondage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_sondages_reponses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `question_suivante_id` int(11) DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nb_votes` int(11) NOT NULL,
  `ordre` tinyint(4) NOT NULL,
  `question_suivante` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`),
  KEY `question_suivante_id` (`question_suivante_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_sondages_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(10) unsigned DEFAULT NULL,
  `reponse_id` int(11) DEFAULT NULL,
  `question_id` int(11) NOT NULL,
  `texte_id` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `ip` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `reponse_id` (`reponse_id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_sondages_votes_textes` (
  `vote_id` int(11) NOT NULL,
  `texte` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`vote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_statistiques` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `creation` datetime NOT NULL,
  `rang_global` int(11) NOT NULL,
  `rang_france` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_tag_citation` (
  `tag_id` bigint(20) NOT NULL DEFAULT '0',
  `citation_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tag_id`,`citation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) DEFAULT NULL,
  `nom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `couleur` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `moderation` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_tentatives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `ip` int(11) NOT NULL,
  `blocage` tinyint(1) NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_tracker_feedback` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` bigint(20) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contenu` text COLLATE utf8_unicode_ci,
  `ip` bigint(20) DEFAULT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_tracker_tickets` (
  `ticket_id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id_utilisateur` int(11) NOT NULL,
  `ticket_id_version_first` int(11) NOT NULL,
  `ticket_id_version_courante` int(11) NOT NULL,
  `ticket_titre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ticket_description` text COLLATE utf8_unicode_ci NOT NULL,
  `ticket_date` datetime NOT NULL,
  `ticket_prive` tinyint(1) NOT NULL,
  `ticket_critique` tinyint(1) NOT NULL,
  `ticket_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ticket_id_revision` int(11) NOT NULL,
  `ticket_id_doublon` int(11) DEFAULT NULL,
  `ticket_type` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `ticket_user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_tracker_tickets_flags` (
  `lunonlu_id_utilisateur` int(11) NOT NULL,
  `lunonlu_id_ticket` int(11) NOT NULL,
  `lunonlu_id_version` int(11) NOT NULL,
  `lunonlu_suivi` tinyint(1) NOT NULL,
  `lunonlu_suivi_envoye` tinyint(1) NOT NULL,
  PRIMARY KEY (`lunonlu_id_utilisateur`,`lunonlu_id_ticket`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_tracker_tickets_versions` (
  `version_id` int(11) NOT NULL AUTO_INCREMENT,
  `version_id_ticket` int(11) NOT NULL,
  `version_id_utilisateur` int(11) NOT NULL,
  `version_id_categorie_concernee` int(11) NOT NULL,
  `version_id_admin` int(11) DEFAULT NULL,
  `version_id_projet` int(11) DEFAULT NULL,
  `version_date` datetime NOT NULL,
  `version_priorite` tinyint(4) NOT NULL,
  `version_resume` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `version_commentaire` text COLLATE utf8_unicode_ci,
  `version_ip` int(11) NOT NULL,
  `version_etat` tinyint(4) NOT NULL,
  PRIMARY KEY (`version_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_twitter_comptes` (
  `id` bigint(20) unsigned NOT NULL,
  `nom` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `access_key` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `access_secret` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `creation` datetime NOT NULL,
  `tweets` int(10) unsigned NOT NULL,
  `dernier_tweet` bigint(20) unsigned DEFAULT NULL,
  `par_defaut` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_twitter_mentions` (
  `id` bigint(20) NOT NULL DEFAULT '0',
  `compte_id` bigint(20) NOT NULL,
  `nouvelle` tinyint(1) NOT NULL DEFAULT '1',
  `creation` datetime DEFAULT NULL,
  `pseudo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `texte` text COLLATE utf8_unicode_ci,
  `reponse_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_twitter_tweets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `twitter_id` bigint(20) unsigned NOT NULL,
  `compte_id` bigint(20) unsigned NOT NULL,
  `utilisateur_id` int(10) unsigned NOT NULL,
  `creation` datetime NOT NULL,
  `texte` varchar(240) COLLATE utf8_unicode_ci NOT NULL,
  `programmation` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `twitter_id` (`twitter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_utilisateurs` (
  `utilisateur_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `utilisateur_pseudo` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_nouvel_email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_avatar` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_mot_de_passe` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_nouveau_mot_de_passe` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_signature` text COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_forum_messages` int(11) NOT NULL DEFAULT '0',
  `utilisateur_date_inscription` datetime NOT NULL,
  `utilisateur_date_derniere_visite` datetime NOT NULL,
  `utilisateur_valide` tinyint(1) NOT NULL,
  `utilisateur_id_groupe` tinyint(3) NOT NULL,
  `utilisateur_hash_validation` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_hash_validation2` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_ip` int(10) NOT NULL,
  `utilisateur_titre` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_adresse` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_latitude` decimal(10,6) NOT NULL,
  `utilisateur_longitude` decimal(10,6) NOT NULL,
  `utilisateur_nb_sanctions` tinyint(4) NOT NULL,
  `utilisateur_pourcentage` tinyint(3) NOT NULL,
  `utilisateur_afficher_mail` tinyint(1) NOT NULL,
  `utilisateur_profession` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_date_naissance` date NOT NULL,
  `utilisateur_site_web` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_localisation` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_biographie` text COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_passions` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_citation` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_absent` tinyint(1) NOT NULL,
  `utilisateur_debut_absence` date DEFAULT NULL,
  `utilisateur_fin_absence` datetime DEFAULT NULL,
  `utilisateur_motif_absence` text COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_afficher_pays` tinyint(1) NOT NULL,
  `utilisateur_derniere_lecture` datetime NOT NULL,
  `utilisateur_cle_pgp` text COLLATE utf8_unicode_ci NOT NULL,
  `utilisateur_sexe` tinyint(1) NOT NULL,
  PRIMARY KEY (`utilisateur_id`),
  UNIQUE KEY `utilisateur_pseudo` (`utilisateur_pseudo`),
  KEY `id_groupe` (`utilisateur_id_groupe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_utilisateurs_ips` (
  `ip_id_utilisateur` int(11) NOT NULL,
  `ip_ip` bigint(20) NOT NULL,
  `ip_date_debut` datetime NOT NULL,
  `ip_date_last` datetime NOT NULL,
  `ip_localisation` tinyint(4) NOT NULL,
  `ip_proxy` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`ip_id_utilisateur`,`ip_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_utilisateurs_preferences` (
  `preference_id_utilisateur` int(11) NOT NULL,
  `preference_activer_rep_rapide` tinyint(1) NOT NULL,
  `preference_afficher_admin_rapide` tinyint(1) NOT NULL,
  `preference_afficher_signatures` tinyint(1) NOT NULL,
  `preference_activer_email_mp` tinyint(1) NOT NULL DEFAULT '0',
  `preference_temps_redirection` tinyint(1) NOT NULL,
  `preference_debug` tinyint(1) NOT NULL,
  `preference_decalage` int(11) NOT NULL,
  `preference_beta_tests` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`preference_id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `zcov2_zingle_logs_flux` (
  `log_action` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `log_module` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `log_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `log_ip` int(11) NOT NULL,
  `log_date` datetime NOT NULL,
  `log_nb_views` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `log_user_agent` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`log_action`,`log_module`,`log_ip`,`log_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
