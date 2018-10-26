<?php

namespace Zco\Bundle\BlogBundle\Admin;

use Zco\Bundle\AdminBundle\PendingTask;

/**
 * Counts the number of submitted articles.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
final class ArticlesPendingTask implements PendingTask
{
    public function count(): int
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare('SELECT COUNT(1) 
            FROM zcov2_blog 
            WHERE blog_etat IN(' . BLOG_PROPOSE . ',' . BLOG_PREPARATION . ')');
        $stmt->execute();
        $res = $stmt->fetchColumn();
        $stmt->closeCursor();

        return (int)$res;
    }

    public function getCredentials(): array
    {
        return ['blog_valider'];
    }
}