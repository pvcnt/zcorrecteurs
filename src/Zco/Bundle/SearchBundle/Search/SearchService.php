<?php

namespace Zco\Bundle\SearchBundle\Search;

use Zco\Bundle\CategoriesBundle\Domain\CategoryDAO;
use Zco\Bundle\SearchBundle\Search\Searchable\SearchableInterface;
use Zco\Bundle\UserBundle\Domain\UserDAO;

class SearchService
{
    private $searchEngine;

    /**
     * Constructor.
     *
     * @param SearchEngineInterface $searchEngine
     */
    public function __construct(SearchEngineInterface $searchEngine)
    {
        $this->searchEngine = $searchEngine;
    }

    public function analyze(SearchQuery $query, SearchableInterface $searchable)
    {
        if ($searchable->doesCheckCredentials()) {
            $viewableCategoryIds = [];
            foreach (CategoryDAO::ListerCategories(true) as $cat) {
                $viewableCategoryIds[] = $cat['cat_id'];
            }
            if ($query->getCategoryIds()) {
                $categoryId = array_intersect($query->getCategoryIds(), $viewableCategoryIds);
                $query->setCategories($categoryId);
            } else {
                $query->setCategories($viewableCategoryIds);
            }
        }

        if ($query->getAuthor()) {
            $user = UserDAO::InfosUtilisateur($query->getAuthor());
            $query->setAuthor($user ? (int)$user['utilisateur_id'] : -1);
        }

        return $query;
    }

    public final function execute(SearchQuery $query, SearchableInterface $searchable)
    {
        $this->analyze($query, $searchable);
        $res = $this->searchEngine->execute($query, $searchable->getIndex());
        $transformedResults = $res->getResults() ? $searchable->transformResults($res->getResults()) : [];

        return new SearchResult($res->getTotalCount(), $transformedResults);
    }
}