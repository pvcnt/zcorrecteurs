<?php

namespace Zco\Bundle\ContentBundle\Search;

class SearchQuery
{
    const MATCH_ALL = 1;
    const MATCH_ANY = 2;
    const MATCH_BOOLEAN = 3;
    const MATCH_PHRASE = 4;

    const SORT_ASC = 1;
    const SORT_DESC = 2;
    const SORT_RELEVANCE = 3;

    const RESULTS_PER_PAGE = 20;

    private $matchMode = self::MATCH_ALL;
    private $sortMode = self::SORT_RELEVANCE;
    private $sortKey;
    private $limit = 50;
    private $offset = 0;
    private $author;
    private $categoryIds = [];
    private $flags = [];
    private $search = '';

    /**
     * @param string $author
     * @return SearchQuery Current instance.
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @param int|array $categoryIds
     * @return SearchQuery Current instance.
     */
    public function setCategories($categoryIds)
    {
        if (is_array($categoryIds)) {
            $this->categoryIds = $categoryIds;
        } else {
            $this->categoryIds = [$categoryIds];
        }

        return $this;
    }

    /**
     * @param string $mode
     * @param string|null $key
     * @return SearchQuery Current instance.
     */
    public function setOrderBy($mode, $key = null)
    {
        if (($mode === self::SORT_ASC || $mode === self::SORT_DESC) && $key === null) {
            $modeH = $mode === self::SORT_ASC ? 'SORT_ASC' : 'SORT_DESC';
            throw new \BadMethodCallException(sprintf('Sorting mode %s requires a key to be given', $modeH));
        }
        if (!in_array($mode, [self::SORT_ASC, self::SORT_DESC, self::SORT_RELEVANCE])) {
            throw new \InvalidArgumentException(sprintf('Sort mode %s is not supported', $mode));
        }
        $this->sortMode = $mode;
        $this->sortKey = $key;

        return $this;
    }

    /**
     * @param int $limit
     * @return SearchQuery Current instance.
     */
    public function setLimit($limit)
    {
        if ($limit <= 0) {
            throw new \InvalidArgumentException(sprintf('The number of results must be greater than 0 (got %s)', $limit));
        }
        $this->limit = (int)$limit;

        return $this;
    }

    /**
     * @param $page
     * @param int $perPage
     * @return SearchQuery Current instance.
     */
    public function setPage($page, $perPage = self::RESULTS_PER_PAGE)
    {
        return $this->setLimit($perPage)->setOffset(($page - 1) * $perPage);
    }

    /**
     * @param int $offset
     * @return SearchQuery Current instance.
     */
    public function setOffset($offset)
    {
        if ($offset < 0) {
            throw new \InvalidArgumentException(sprintf('The offset has to be a positive integer (got %s)', $offset));
        }
        $this->offset = (int)$offset;

        return $this;
    }

    public function includeFlag($key)
    {
        $this->flags[$key] = true;

        return $this;
    }

    public function excludeFlag($key)
    {
        $this->flags[$key] = false;

        return $this;
    }

    public function setMatchMode($matchMode)
    {
        if (!in_array($matchMode, [self::MATCH_ALL, self::MATCH_ANY, self::MATCH_BOOLEAN, self::MATCH_PHRASE])) {
            throw new \InvalidArgumentException(sprintf('Match mode %s is not supported', $matchMode));
        }
        $this->matchMode = $matchMode;

        return $this;
    }

    /**
     * @param string $search
     * @return SearchQuery Current instance.
     */
    public function setSearch($search)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @return int
     */
    public function getMatchMode()
    {
        return $this->matchMode;
    }

    /**
     * @return int
     */
    public function getSortMode()
    {
        return $this->sortMode;
    }

    /**
     * @return string|null
     */
    public function getSortKey()
    {
        return $this->sortKey;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return null|string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return array
     */
    public function getCategoryIds()
    {
        return $this->categoryIds;
    }

    /**
     * @return array
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }
}