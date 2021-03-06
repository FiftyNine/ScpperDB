<?php

namespace Application\Mapper;

use Application\Utils\VoteType;

interface VoteMapperInterface extends SimpleMapperInterface
{
    /**
     * Returns number of site votes
     * @param int $siteId
     * @param int $type
     * @param \DateTime $castAfter Count only votes cast after date
     * @param \DateTime $castBefore Count only votes cast before date
     * @return int
     */
    public function countSiteVotes($siteId, $type = VoteType::ANY, \DateTime $castAfter = null, \DateTime $castBefore = null);

    /**
     * Return votes for a certain site
     * @param int $siteId
     * @param array[string]int
     * @param bool $paginated
     * @return Zend\Paginator\Paginator|VoteInterface[]
     */
    public function findSiteVotes($siteId, $order = null, $paginated = false);
    
    /**
     * Return votes for a certain page
     * @param int $pageId
     * @param array[string]int
     * @param bool $paginated
     * @return Zend\Paginator\Paginator|VoteInterface[]
     */
    public function findVotesOnPage($pageId, $order = null, $paginated = false);
    
    /**
     * Return votes of a certain user
     * @param int $userId
     * @param int $siteId
     * @param array[string]int
     * @param bool $paginated
     * @return Zend\Paginator\Paginator|VoteInterface[]
     */
    public function findVotesOfUser($userId, $siteId, $order = null, $paginated = false);    
    
    /**
     * Get an aggregated results from votes
     * @param array[string]string
     * @param \Application\Utils\QueryAggregateInterface[] $aggregates
     * @param \DateTime $castAfter
     * @param \DateTime $castBefore
     * @param bool $deleted
     * @param array(string => int) $order Associative array of field names and sorting orders (constants from \Application\Utils\Order)
     * @param bool $paginated Return a \Zend\Paginator\Paginator object instead of actual objects
     * @return array(array(string => mixed))
     */
    public function getAggregatedValues($conditions, $aggregates, \DateTime $castAfter = null, \DateTime $castBefore = null, $deleted = null, $order = null, $paginated = false);
    
    /**
     * Get an aggregated results from votes on specific author
     * @param int $userId
     * @param int $siteId
     * @param \Application\Utils\QueryAggregateInterface[] $aggregates
     * @param array(string => int) $order Associative array of field names and sorting orders (constants from \Application\Utils\Order)
     * @param bool $paginated Return a \Zend\Paginator\Paginator object instead of actual objects
     * @return array(array(string => mixed))
     */
    public function getAggregatedVotesOnUser($userId, $siteId, $aggregates, $order = null, $paginated = false);
    
    /**
     * Get a list of favorite authors of user
     * @param int $userId
     * @param int $siteId
     * @param bool $orderByRatio Order by total or by ratio
     * @param bool $paginated Return a \Zend\Paginator\Paginator object instead of actual objects
     * @return array(array(string => mixed))
     */
    public function getUserFavoriteAuthors($userId, $siteId, $orderByRatio, $paginated = false);

    /**
     * Get an aggregated results from votes
     * P.e. Get a number of votes
     * @param int $userId
     * @param int $siteId
     * @param bool $orderByRatio Order by total or by ratio
     * @param bool $paginated
     * @return array[array[string]mixed]
     */
    public function getUserFavoriteTags($userId, $siteId, $orderByRatio, $paginated = false);
    
    /**
     * Get biggest fans of user
     * @param int $userId
     * @param int $siteId
     * @param bool $orderByRatio Order by total or by ratio
     * @param bool $paginated
     * @return array[array[string]mixed]
     */
    public function getUserBiggestFans($userId, $siteId, $orderByRatio, $paginated = false);
    
    
    /**
     * Returns aggregated votes for a rating chart grouped by day
     * @param int $pageId
     * @return array(array(string => mixed)) Array of votes grouped by date
     */
    public function getPageChartData($pageId);

    /**
     * Returns aggregated votes for a rating chart grouped by day
     * @param int $userId
     * @param int $siteId
     * @return array(array(string => mixed)) Array of votes grouped by date
     */
    public function getUserChartData($userId, $siteId);    
    
    /**
     * Return history of votes (excluding current vote by the user on the page
     * @param int $pageId
     * @param int $userId
     * @return VoteInterface[]
     */
    public function findHistory($pageId, $userId);
}