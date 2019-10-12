<?php

use Symfony\Component\Console\Output\OutputInterface;
use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;

/**
 * Delete obsolete credentials.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Version20191012000000 extends AbstractMigration
{
    public function up(OutputInterface $output)
    {
        $credentials = [
            'options_editer_defaut',
            'lister_blocages',
            'groupes_changer_droits',
            'bannir_mails',
            'recrutements_editer',
            'recrutements_supprimer',
            'recrutements_supprimer_candidatures',
            'recrutements_voir_candidatures',
            'recrutements_voir_tests',
            'recrutements_repondre',
            'recrutements_voir_commentaire',
            'recrutements_voir_prives',
            'mp_espionner',
            'mp_quota',
            'anti_up',
            'epargne_anti_up',
            'mp_limite_participants',
            'mp_nb_participants_max',
            'membres_editer_titre',
            'ips_analyser',
            'ips_bannir',
            'dictees_ajouter',
            'dictees_editer',
            'dictees_voir_toutes',
            'dictees_supprimer_toutes',
            'dictees_editer_toutes',
            'voir_sanctions',
            'sanctionner',
            'voir_coms_billets_proposes',
            'blog_choisir_comms',
            'blog_poster_commentaires_fermes',
            'blog_choisir_etat',
            'blog_devalider',
            'blog_editer_valide',
            'mettre_sujets_coup_coeur',
            'fusionner_sujets',
            'diviser_sujets',
            'voir_alertes',
            'signaler_sujets',
            'mettre_sujet_favori',
            'membres_voir_ch_pseudos',
            'membres_valider_ch_pseudos',
            'gerer_breve_accueil',
            'code',
            'blog_voir_refus',
            'blog_voir_billets_proposes',
            'blog_voir_versions',
            'blog_voir_historique',
            'blog_editer_preparation',
            'blog_editer_ses_commentaires',
            'mp_signaler',
            'mp_repondre_mp_fermes',
            'mp_fermer',
            'mp_tous_droits_participants',
            'mp_alertes',
            'membres_editer_pseudos',
            'resolu_ses_sujets',
            'editer_ses_messages',
            'droits_gerer',
            'membres_editer_propre_titre',
            'membres_editer_titre',
            'mp_editer_ses_messages_deja_lus',
            'recrutements_attribuer_copie',
            'recrutements_desattribuer_copie',
            'recrutements_desistement',
            'recrutements_ecrire_shoutbox',
            'recrutements_voir_shoutbox',
            'recrutements_avis',
            'indiquer_ses_messages_aide',
        ];

        $stmt = $this->dbh->prepare('SELECT droit_id FROM zcov2_droits WHERE droit_nom IN("' . implode('", "', $credentials) . '")');
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $ids = [];
        foreach ($rows as $i => $row) {
            $ids[] = $row['droit_id'];
        }

        $this->addSql('DELETE FROM zcov2_groupes_droits WHERE gd_id_droit IN(' . implode(', ', $ids) . ')');
        $this->addSql('DELETE FROM zcov2_droits WHERE droit_id IN(' . implode(', ', $ids) . ')');
    }

    public function down(OutputInterface $output)
    {
        $this->throwIrreversibleMigrationException();
    }
}