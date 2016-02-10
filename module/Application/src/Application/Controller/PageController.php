<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Service\HubServiceInterface;
use Application\Factory\Component\PaginatedTableFactory;
use Application\Utils\Aggregate;
use Application\Utils\DateAggregate;
use Application\Utils\Order;
use Application\Utils\DbConsts\DbViewVotes;
use Application\Utils\DbConsts\DbViewRevisions;

/**
 * Description of PageController
 *
 * @author Alexander
 */
class PageController extends AbstractActionController 
{
    /**
     *
     * @var Application\Service\ServiceHubInterface
     */    
    protected $services;

    /**
     * Returns a paginated table with revisions
     * @param int $pageId
     * @param int $orderBy
     * @param string $order
     * @param int $page
     * @param int $perPage
     * @return Application\Component\PaginatedTable\TableInterface
     */
    protected function getRevisionsTable($pageId, $orderBy, $order, $page, $perPage)
    {
        $revisions = $this->services->getRevisionService()->findRevisionsOfPage($pageId, array($orderBy => $order), true, $page, $perPage);
        $table = PaginatedTableFactory::createRevisionsTable($revisions);
        $table->getColumns()->setOrder($orderBy, $order === Order::ASCENDING);        
        return $table;        
    }

    /**
     * Returns a paginated table with votes
     * @param int $pageId
     * @param string $orderBy
     * @param int $order
     * @param int $page
     * @param int $perPage
     * @return Application\Component\PaginatedTable\TableInterface
     */
    protected function getVotesTable($pageId, $orderBy, $order, $page, $perPage)
    {
        $votes = $this->services->getVoteService()->findVotesOnPage($pageId, array($orderBy => $order), true, $page, $perPage);
        $table = PaginatedTableFactory::createVotesTable($votes);
        $table->getColumns()->setOrder($orderBy, $order === Order::ASCENDING);
        return $table;
    }
    
    public function __construct(HubServiceInterface $services) 
    {
        $this->services = $services;
    }

    public function pageAction()
    {
        $pageId = (int)$this->params()->fromRoute('pageId');
        $page = $this->services->getPageService()->find($pageId);
        if (!$page) {
            return $this->notFoundAction();
        }
        return new ViewModel(array(
            'page' => $page,
            'revisions' => $this->getRevisionsTable($pageId, DbViewRevisions::REVISIONINDEX, Order::DESCENDING, 1, 10),
            'votes' => $this->getVotesTable($pageId, DbViewVotes::DATETIME, Order::DESCENDING, 1, 10)
        ));
    }
    
    public function ratingChartAction()
    {
        $pageId = (int)$this->params()->fromQuery('pageId');
        $byDate = new DateAggregate(DbViewVotes::DATETIME, 'Date');
        $count = new Aggregate(DbViewVotes::VALUE, Aggregate::SUM, 'Votes');
        $votes = $this->services->getVoteService()->getAggregatedForPage($pageId, array($byDate, $count), true);
        $resVotes = array();
        foreach ($votes as $vote) {
            $resVotes[] = array($vote['Date']->format(\DateTime::ISO8601), (int)$vote['Votes']);
        }
        $revisions = $this->services->getRevisionService()->findRevisionsOfPage($pageId);
        $resRevisions = array();
        foreach ($revisions as $rev) {
            $resRevisions[] = array(
                $rev->getDateTime()->format(\DateTime::ISO8601), 
                array(
                    'name' => (string)($rev->getIndex()+1),
                    'text' => $rev->getComments()==='' ? $rev->getUser()->getDisplayName() : sprintf('%s: "%s"', $rev->getUser()->getDisplayName(), $rev->getComments())
                )
            );
        }
        return new JsonModel(array(
            'success' => true,
            'votes' => $resVotes,
            'milestones' => $resRevisions,
        ));
    }
    
    public function revisionListAction()
    {
        $pageId = (int)$this->params()->fromQuery('pageId');
        $page = (int)$this->params()->fromQuery('page', 1);
        $perPage = (int)$this->params()->fromQuery('perPage', 10);
        $orderBy = $this->params()->fromQuery('orderBy', DbViewRevisions::REVISIONINDEX);
        $order = $this->params()->fromQuery('ascending', true);
        if ($order) {
            $order = Order::ASCENDING;
        } else {
            $order = Order::DESCENDING;
        }    
        $table = $this->getRevisionsTable($pageId, $orderBy, $order, $page, $perPage);
        $renderer = $this->getServiceLocator()->get('ViewHelperManager')->get('partial');
        if ($renderer) {
            $result['success'] = true;                
            $result['content'] = $renderer(
                'partial/tables/table.phtml', 
                array(
                    'table' => $table, 
                    'data' => array()
                )
            );
        }
        return new JsonModel($result);                
    }
    
    public function voteListAction()
    {
        $pageId = (int)$this->params()->fromQuery('pageId');
        $page = (int)$this->params()->fromQuery('page', 1);
        $perPage = (int)$this->params()->fromQuery('perPage', 10);
        $orderBy = $this->params()->fromQuery('orderBy', DbViewVotes::DATETIME);
        $order = $this->params()->fromQuery('ascending', true);
        if ($order) {
            $order = Order::ASCENDING;
        } else {
            $order = Order::DESCENDING;
        }    
        $table = $this->getVotesTable($pageId, $orderBy, $order, $page, $perPage);
        $renderer = $this->getServiceLocator()->get('ViewHelperManager')->get('partial');
        if ($renderer) {
            $result['success'] = true;                
            $result['content'] = $renderer(
                'partial/tables/table.phtml', 
                array(
                    'table' => $table, 
                    'data' => array()
                )
            );
        }
        return new JsonModel($result);                
    }
}
