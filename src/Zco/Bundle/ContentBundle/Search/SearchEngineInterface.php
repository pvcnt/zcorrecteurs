<?php

namespace Zco\Bundle\ContentBundle\Search;

interface SearchEngineInterface
{
    /**
     * @param SearchQuery $query
     * @param string $index
     * @return SearchResult
     */
    function execute(SearchQuery $query, $index);
}