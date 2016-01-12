<?php

namespace Application\Service;

use Application\Mapper\PageMapperInterface;
use Application\Utils\PageType;
use Application\Utils\DateGroupType;

class PageService implements PageServiceInterface 
{
    /**
     *
     * @var PageMapperInterface
     */
    protected $mapper;
    
    public function __construct(PageMapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }
    
    /**
     * {@inheritDoc}
     */
    public function find($id)
    {
        return $this->mapper->find($id);
    }
    
    /**
     * {@inheritDoc}
     */    
    public function findAll()
    {
        return $this->mapper->findAll();
    }
    
    /**
     * {@inheritDoc}
     */
    public function countSitePages($siteId, $type = PageType::ANY, \DateTime $createdAfter = null, \DateTime $createdBefore = null)
    {
        return $this->mapper->countSitePages($siteId, $type, $createdAfter, $createdBefore);
    }
    
    /**
     * {@inheritDoc}
     */
    public function findSitePages($siteId, $type = PageType::ANY, \DateTime $createdAfter = null, \DateTime $createdBefore = null, $offset = 0, $limit = 0)
    {
        return $this->mapper->findSitePages($siteId, $type, $createdAfter, $createdBefore, $offset, $limit);
    }
    
    /**
     * {@inheritDoc}
     */
    public function countCreatedPages($siteId, \DateTime $createdAfter, \DateTime $createdBefore)
    {
        $group = DateGroupType::getBestGroupType($createdAfter, $createdBefore);
        return $this->mapper->countCreatedPages($siteId, $createdAfter, $createdBefore, $group);        
    }            
}