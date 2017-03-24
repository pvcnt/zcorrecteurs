<?php

namespace Zco\Bundle\SearchBundle\Search;

class SearchResult
{
    private $totalCount;
    private $results;

    /**
     * Constructor.
     *
     * @param int $totalCount
     * @param array $results
     */
    public function __construct($totalCount, array $results)
    {
        $this->totalCount = $totalCount;
        $this->results = $results;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }
}