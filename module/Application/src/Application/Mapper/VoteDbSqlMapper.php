<?php

namespace Application\Mapper;

use Zend\Db\Sql\Sql;
use Application\Utils\VoteType;
use Application\Utils\DbConsts\DbViewVotes;

class VoteDbSqlMapper extends ZendDbSqlMapper implements VoteMapperInterface
{
    protected function buildVoteSelect(Sql $sql, $siteId, $type = VoteType::ANY, \DateTime $castAfter = null, \DateTime $castBefore = null)
    {
        $select = $sql->select()
                      ->from(array('v' => DbViewVotes::TABLE))
                      ->where(array('v.'.DbViewVotes::SITEID.' = ?' => $siteId));
        if ($type !== VoteType::ANY) {
            $select->where(array('v.'.DbViewVotes::VALUE.' = ?' => $type));
        }
        if ($castAfter) {
            $select->where(array('v.'.DbViewVotes::DATETIME.' >= ?' => $castAfter->format(self::DATETIME_FORMAT)));
        }
        if ($castBefore) {
            $select->where(array('v.'.DbViewVotes::DATETIME.' <= ?' => $castBefore->format(self::DATETIME_FORMAT)));
        }
        return $select;
    }
    
    /**
     * 
     * {@inheritDoc}
     */
    public function countSiteVotes($siteId, $type = VoteType::ANY, \DateTime $castAfter = null, \DateTime $castBefore = null) 
    {
        $sql = new Sql($this->dbAdapter);
        $select = $this->buildVoteSelect($sql, $siteId, $type, $castAfter, $castBefore);
        return $this->fetchCount($sql, $select);
    }

    /**
     * 
     * {@inheritDoc}
     */    
    public function getAggregatedValues($siteId, $aggregates, \DateTime $castAfter, \DateTime $castBefore, $order = null, $paginated = false)
    {
        $sql = new Sql($this->dbAdapter);
        $select = $this->buildVoteSelect($sql, $siteId, VoteType::ANY, $castAfter, $castBefore);
        $this->aggregateSelect($select, $aggregates);
        if (is_array($order)) {
            $this->orderSelect($select, $order);
        }
        if ($paginated) {
            return $this->getPaginator($select, true);
        }        
        return $this->fetchArray($sql, $select);
    }
}