<?php

namespace Zco\Bundle\BlogBundle\Admin;

use Zco\Bundle\AdminBundle\PendingTask;
use Zco\Bundle\BlogBundle\Domain\BlogDAO;

/**
 * Counts the number of submitted articles.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
final class ArticlesPendingTask implements PendingTask
{
    public function count(): int
    {
        return BlogDAO::NombreBilletsProposes();
    }

    public function getCredentials(): array
    {
        return ['blog_valider'];
    }
}