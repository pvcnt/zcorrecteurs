<?php

namespace Zco\Bundle\SearchBundle\Search\Sphinx;

use Sphinx\SphinxClient;
use Zco\Bundle\SearchBundle\Search\SearchEngineException;
use Zco\Bundle\SearchBundle\Search\SearchEngineInterface;
use Zco\Bundle\SearchBundle\Search\SearchQuery;
use Zco\Bundle\SearchBundle\Search\SearchResult;

final class SphinxSearchEngine implements SearchEngineInterface
{
    private $host;
    private $port;

    private const CATEGORY_FIELD = 'categorie_id';
    private const AUTHOR_FIELD = 'utilisateur_id';

    /**
     * Constructor.
     *
     * @param string $host
     * @param int $port
     */
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(SearchQuery $query, $index)
    {
        $client = new SphinxClient();
        $client->setServer($this->host, $this->port);
        $client->setConnectTimeout(1);
        $client->setMaxQueryTime(5000); // 5 seconds
        $client->setArrayResult(true);
        //$this->client->setIndexWeights(array('message_texte' => 100));
        $client->setRankingMode(SPH_RANK_PROXIMITY_BM25);

        $matchModes = [
            SearchQuery::MATCH_ALL => SPH_MATCH_ALL,
            SearchQuery::MATCH_ANY => SPH_MATCH_ANY,
            SearchQuery::MATCH_BOOLEAN => SPH_MATCH_BOOLEAN,
            SearchQuery::MATCH_PHRASE => SPH_MATCH_PHRASE
        ];
        $sortModes = [
            SearchQuery::SORT_ASC => SPH_SORT_ATTR_ASC,
            SearchQuery::SORT_DESC => SPH_SORT_ATTR_DESC,
            SearchQuery::SORT_RELEVANCE => SPH_SORT_RELEVANCE,
        ];
        $client->setMatchMode($matchModes[$query->getMatchMode()]);
        $client->setSortMode($sortModes[$query->getSortMode()], ($query->getSortKey() === null ? '' : $query->getSortKey()));
        $client->setLimits($query->getOffset(), $query->getLimit());
        foreach ($query->getFlags() as $key => $value) {
            $client->setFilter($key, $value ? [1] : [0]);
        }
        if ($query->getAuthor()) {
            $client->setFilter(self::AUTHOR_FIELD, [$query->getAuthor()]);
        }
        if ($query->getCategoryIds()) {
            $client->setFilter(self::CATEGORY_FIELD, $query->getCategoryIds());
        }

        $res = $client->query($query->getSearch(), $index);
        if ($client->_error) {
            throw new SearchEngineException($client->_error);
        }
        $results = $res['matches'] ?? [];

        return new SearchResult($res['total'], $results);
    }
}