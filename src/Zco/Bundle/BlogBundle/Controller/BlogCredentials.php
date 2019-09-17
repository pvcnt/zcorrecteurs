<?php

namespace Zco\Bundle\BlogBundle\Controller;

final class BlogCredentials
{
    private $isAllowed = false;
    private $isContributor = false;
    private $isOwner = false;

    private $canView = false;
    private $canEdit = false;
    private $canDelete = false;
    private $canManage = false;
    private $canPublish = false;
    private $canUnpublish = false;

    /**
     * Constructor.
     *
     * @param array $authors
     * @param array $article
     */
    public function __construct($authors, $article)
    {
        foreach ($authors as $a) {
            if ($a['utilisateur_id'] == $_SESSION['id']) {
                $this->isAllowed = true;
                if ($a['auteur_statut'] == 3)
                    $this->isOwner = true;
                if ($a['auteur_statut'] > 1)
                    $this->isContributor = true;
            }
        }

        //--- On regarde si le visiteur peut éditer le billet ---
        $this->canEdit = (
            (in_array($article['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE))
                && ($this->isContributor || verifier('blog_editer_brouillons')))
            || ($article['blog_etat'] == BLOG_PREPARATION && verifier('blog_editer_preparation'))
            || ($article['blog_etat'] == BLOG_VALIDE && verifier('blog_editer_valide'))
        );

        //--- On regarde si le visiteur peut voir le billet ---
        $this->canView = (
            //-> Billet en ligne
            ($article['blog_etat'] == BLOG_VALIDE && strtotime($article['blog_date_publication']) <= time())
            //-> Billet programmé
            || ($article['blog_etat'] == BLOG_VALIDE && strtotime($article['blog_date_publication']) >= time() && verifier('blog_valider', $article['blog_id_categorie']))
            //-> Billet proposé ou en préparation par l'équipe
            || (in_array($article['blog_etat'], array(BLOG_PROPOSE, BLOG_PREPARATION)) && verifier('blog_voir_billets_proposes'))
            //-> Billet en rédaction ou bien refusé
            || (in_array($article['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE)) && verifier('blog_voir_billets_redaction'))
            //-> Ou bien si le membre est un rédacteur, il peut toujours voir le billet.
            || $this->isAllowed
        );

        //--- On regarde si le visiteur peut voir l'admin du billet ---
        $this->canManage = ($this->isAllowed || $this->canEdit || ($this->canView && $article['blog_etat'] != BLOG_VALIDE));

        //--- On regarde si le visiteur peut dévalider le billet ---
        $this->canPublish = verifier('blog_valider') && in_array($article['blog_etat'], array(BLOG_VALIDE, BLOG_PREPARATION));
        $this->canUnpublish = verifier('blog_valider') && $article['blog_etat'] == BLOG_VALIDE;

        //--- On regarde si le visiteur peut supprimer le billet ---
        $this->canDelete = (
            verifier('blog_editer_valide')
            || (in_array($article['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE)) && $this->isOwner));
    }

    /**
     * @return bool
     */
    public function isAllowed(): bool
    {
        return $this->isAllowed;
    }

    /**
     * @return bool
     */
    public function isContributor(): bool
    {
        return $this->isContributor;
    }

    /**
     * @return bool
     */
    public function isOwner(): bool
    {
        return $this->isOwner;
    }

    /**
     * @return bool
     */
    public function canView(): bool
    {
        return $this->canView;
    }

    /**
     * @return bool
     */
    public function canEdit(): bool
    {
        return $this->canEdit;
    }

    /**
     * @return bool
     */
    public function canDelete(): bool
    {
        return $this->canDelete;
    }

    /**
     * @return bool
     */
    public function canManage(): bool
    {
        return $this->canManage;
    }

    /**
     * @return bool
     */
    public function canPublish(): bool
    {
        return $this->canPublish;
    }

    /**
     * @return bool
     */
    public function canUnpublish(): bool
    {
        return $this->canUnpublish;
    }
}