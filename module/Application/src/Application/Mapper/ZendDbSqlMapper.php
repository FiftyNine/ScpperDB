<?php

namespace Application\Mapper;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\EventManager\EventManagerAwareInterface;
use Application\Utils\DateGroupType;

class ZendDbSqlMapper implements SimpleMapperInterface, EventManagerAwareInterface
{
    // User trait to avoid implementing basic stuff
    use \Zend\EventManager\EventManagerAwareTrait;

    const DATETIME_FORMAT = 'Y-m-d H:i:s';
    const DATE_FORMAT = 'Y-m-d|';
    const GROUP_SUFFIX = '___GROUP';
    /**
     *
     * @var AdapterInterface
     */
    protected $dbAdapter;
    
    /**
     *
     * @var HydratorInterface
     */
    protected $hydrator;
    
    /**
     *
     * @var mixed
     */
    protected $objectPrototype;
    
    /**
     *
     * @var string
     */
    protected $table;
    
    /**
     * 
     * @var string
     */
    protected $idFieldName;
    
    
    /**
     * Sends event for a logger to write down query text
     * @param Select $select
     */
    protected function logQuery(Select $select)
    {
        $platform = $this->dbAdapter->getPlatform();
        $query = $select->getSqlString($platform);
        $this->getEventManager()->trigger(\Application\Utils\Events::LOG_SQL_QUERY, $this, compact('query'));               
    }
    
    /**
     * 
     * @param AdapterInterface $dbAdapter
     * @param HydratorInterface $hydrator
     * @param Object $objectPrototype
     * @param string $table
     * @param string $idFieldName
     */
    public function __construct(
            AdapterInterface $dbAdapter, 
            HydratorInterface $hydrator, 
            $objectPrototype,
            $table,
            $idFieldName)
    {
        $this->dbAdapter = $dbAdapter;
        $this->hydrator = $hydrator;
        $this->objectPrototype = $objectPrototype;
        $this->table = $table;
        $this->idFieldName = $idFieldName;       
    }

    /**
     * Returns result of a query if it's successful and false otherwise
     * @param Sql $sql
     * @param Select $select
     * @param int $offset
     * @param int $limit
     * @return boolean|ResultInterface
     */
    protected function fetch(Sql $sql, Select $select, $offset = 0, $limit = 0)
    {
        $this->logQuery($select);
        if ($offset > 0) {
            $select->offset($offset);
        }
        if ($limit > 0) {
            $select->limit($limit);
        }                
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();        
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            return $result;
        }        
        return false;
    }

    /**
     * Returns a collection of hydrated objects or false on error
     * @param Sql $sql
     * @param Select $select
     * @param type $offset
     * @param type $limit
     * @return boolean|ResultSet
     */
    protected function fetchResultSet(Sql $sql, Select $select, $offset = 0, $limit = 0)
    {
        $result = $this->fetch($sql, $select, $offset, $limit);
        if ($result) {
            $resultSet = new HydratingResultSet($this->hydrator, $this->objectPrototype);            
            return $resultSet->initialize($result);            
        }
        return false;
    }


    /**
     * Returns result of a query as an array of associative arrays
     * @param Sql $sql
     * @param Select $select
     * @return array(array('Name' => 'Value'))
     */
    protected function fetchArray(Sql $sql, Select $select)
    {
        $dataset = $this->fetch($sql, $select);
        $result = array();
        while ($dataset && $dataset->current()) {
            $result[] = $dataset->current();
            $dataset->next();
        }
        // Convert date strings into DateTime objects
        if ($dataset->getResource() instanceof \PDOStatement) {
            $utcTimeZone = new \DateTimeZone('UTC');
            for ($i=0; $i<$dataset->getFieldCount(); $i++) {
                $column = $dataset->getResource()->getColumnMeta($i);
                if ($column['native_type'] === 'DATE') {
                    foreach ($result as &$row) {
                        $row[$column['name']] = \DateTime::createFromFormat(self::DATE_FORMAT, $row[$column['name']], $utcTimeZone);
                    }
                }
            }
        }
        // Workaround for group aggregates 
        foreach($result as &$row) {
            foreach ($row as $key => $value) {
                if (substr($key, -strlen(self::GROUP_SUFFIX)) === self::GROUP_SUFFIX) {
                    $newKey = substr($key, 0, strlen($key)-strlen(self::GROUP_SUFFIX));
                    $row[$newKey] = $value;
                    unset($row[$key]);
                }
            }
        }
        return $result;                
    }
    
    /**
     * Returns number of records in query
     * 
     * @param Sql $sql
     * @param Select $select
     * @return int
     */
    protected function fetchCount(Sql $sql, Select $select)
    {
        $aggregate = new \Application\Utils\Aggregate('*', \Application\Utils\AggregateType::COUNT, 'Number');
        $this->aggregateSelect($select, array($aggregate));
        $array = $this->fetchArray($sql, $select);
        if (count($array) > 0) {
            return $array[0]['Number'];
        }
        return 0;        
    }   
        

    /**
     * Builds a new aggregated select from normal select
     * @param Sql $sql
     * @param Select $select
     * @param \Application\Utils\Aggregate[] $aggregates
     * @return Select
     */
    protected function aggregateSelect(Select $select, $aggregates)
    {
        $columns = array();
        foreach ($aggregates as $aggregate) {
            $columns[$aggregate->getAggregateName()] = $aggregate->getAggregateExpression();
        }
        $select->columns($columns, false);
        return $select;
    }    
    
    /**
     * Adds a grouping by date to an aggregated select
     * @param Select $select
     * @param string $dateName Name of field with date
     * @param int $groupBy Type of grouping from \Application\Utils\DateGroupType
     * @return Select
     */
    protected function groupSelectByDate(Select $select, $dateName, $groupBy)
    {
        $aggName = $dateName.self::GROUP_SUFFIX;
        $group = DateGroupType::getSqlGroupString($groupBy, $dateName);
        $columns = $select->getRawState(Select::COLUMNS);
        $columns[$aggName] = new Expression($group);
        $select->columns($columns, false)
                ->group($aggName);
        return $select;
    }
    
    /**
     * 
     * {@inheritDoc}
     */
    public function find($id)
    {
        $sql = new Sql($this->dbAdapter);        
        $select = $sql->select($this->table);
        $select->where(array(
            "{$this->idFieldName} = ?" => $id
        ));
        
        $result = $this->fetch($sql, $select);
        if ($result && $result->getAffectedRows()) {
          return $this->hydrator->hydrate($result->current(), $this->objectPrototype);
        }
        throw new \InvalidArgumentException("Object of table {$this->table} with id = {$id} not found");
    }

    /**
     * 
     * {@inheritDoc}
     */
    public function findAll($offset = 0, $limit = 0) {
        $sql = new Sql($this->dbAdapter);        
        $select = $sql->select($this->table);
        $result = $this->fetchResultSet($sql, $select, $offset, $limit);
        if (!$result) {
            $result = array();
        }
        return $result;
    }
    
    /**
     * 
     * {@inheritDoc}
     */
    public function countAll()
    {
        $sql = new Sql($this->dbAdapter);        
        $select = $sql->select($this->table);
        return $this->fetchCount($sql, $select);
    }
}


