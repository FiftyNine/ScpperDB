<?php

namespace Application\Service;

use Application\Model\RevisionInterface;

interface RevisionServiceInterface
{
    /**
     * Returns a single revision
     * 
     * @param int $id Id
     * @return RevisionInterface
     * @throws \InvalidArgumentException
     */
    public function find($id);
    
    /**
     * Returns all revisions
     * 
     * @return array|RevisionInterface[]
     */
    public function findAll();
    
    /**
     * Returns number of site revisions
     * @param int $siteId
     * @param \DateTime $createdAfter Count only revisions created after date
     * @param \DateTime $createdBefore Count only revisions created before date
     * @return int
     */
    public function countSiteRevisions($siteId, \DateTime $createdAfter = null, \DateTime $createdBefore = null);
    
    /**
     * Returns revisions of a page
     * @param int $pageId
     * @param array[string]int $order
     * @param bool $paginated     
     * @param int $page
     * @param int $perPage
     * @return RevisionInterface[]|\Zend\Paginator\Paginator     
     */
    public function findRevisionsOfPage($pageId, $order = null, $paginated = false, $page = -1, $perPage = -1);
    
    /**
     * Get an aggregated votes from revisions, grouped by creation date
     * P.e. Get a number of votes
     * 
     * @param int $siteId
     * @param \Application\Utils\QueryAggregateInterface[] $aggregates
     * @param \DateTime $createdAfter
     * @param \DateTime $createdBefore
     * @param array(string => int) $order Associative array of field names and sorting orders (constants from \Application\Utils\Order)
     * @param bool $paginated Return a \Zend\Paginator\Paginator object instead of actual objects
     * @return array(array(string => mixed))
     */
    public function getAggregatedValues($siteId, $aggregates, \DateTime $createdAfter = null, \DateTime $createdBefore = null, $order = null, $paginated = false);    
}