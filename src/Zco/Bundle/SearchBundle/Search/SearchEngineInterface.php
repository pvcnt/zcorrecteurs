<?php

namespace Zco\Bundle\SearchBundle\Search;

interface SearchEngineInterface
{
    /**
     * @param SearchQuery $query
     * @param string $index
     * @return SearchResult
     */
    function execute(SearchQuery $query, $index);
}