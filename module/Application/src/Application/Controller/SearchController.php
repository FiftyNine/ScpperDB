<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Application\Service\HubServiceInterface;
use Application\Factory\Component\PaginatedTableFactory;
use Application\Utils\DbConsts\DbViewPages;
use Application\Utils\Order;
use Application\Form\SearchForm;

/**
 * Description of SearchController
 *
 * @author Alexander
 */
class SearchController extends AbstractActionController
{
    /**
     *
     * @var HubServiceInterface 
     */
    protected $services;
    

    /**
     * @param string $mask
     * @param int[] $siteIds
     * @param string $orderBy
     * @param int $order
     * @param int $page
     * @param int $perPage
     * @return Application\Component\TableInterface
     */
    protected function getPagesTable($mask, $siteIds, $deleted = false, $orderBy = null, $order = null, $page = 1, $perPage = 10)
    {
        if ($order === null) {
            $sortOrder = null;
        } else {
            $sortOrder = [$orderBy => $order];
        }
        $pages = $this->services->getPageService()->findByName($mask, $siteIds, $deleted, $sortOrder, true, $page, $perPage);
        $pages->setCurrentPageNumber($page);        
        if ($siteIds === null) {
            $table = PaginatedTableFactory::createSitesPagesTable($pages);            
        } else {
            $table = PaginatedTableFactory::createPagesTable($pages);
        }
        if ($sortOrder) {
            $table->getColumns()->setOrder($orderBy, $order === Order::ASCENDING);        
        }
        return $table;
    }

    /**
     * @param string $mask
     * @param int[] $siteId
     * @param string $orderBy
     * @param int $order
     * @param int $page
     * @param int $perPage
     * @return Application\Component\TableInterface
     */
    protected function getUsersTable($mask, $siteId, $orderBy = null, $order = null, $page = 1, $perPage = 10)
    {
        if ($order === null) {
            $sortOrder = null;
        } else {
            $sortOrder = [$orderBy => $order];
        }
        if ($siteId) {
            $users = $this->services->getUserService()->findUsersOfSiteByName($siteId, $mask, $sortOrder, true);
            $table = \Application\Factory\Component\PaginatedTableFactory::createSiteUsersTable($users);
        } else {
            $users = $this->services->getUserService()->findByName($mask, $sortOrder, true);
            $table = \Application\Factory\Component\PaginatedTableFactory::createUsersTable($users);
        }
        $users->setItemCountPerPage($perPage);
        $users->setCurrentPageNumber($page);
        if ($sortOrder) {
            $table->getColumns()->setOrder($orderBy, $order === Order::ASCENDING);        
        }
        return $table;
    }
    
    public function __construct(HubServiceInterface $hubService) 
    {
        $this->services = $hubService;
    }

    public function searchAction()
    {        
        $request = $this->getRequest();
        $form = $this->services->getUtilityService()->getSearchForm();
        $currentSiteId = $this->services->getUtilityService()->getSiteId();
        $siteId = $request->getPost(SearchForm::SITE_FIELD_NAME, $currentSiteId);
        $allBranches = (bool) $request->getPost(SearchForm::ALL_BRANCHES_NAME);
        $withDeleted = (bool) $request->getPost(SearchForm::WITH_DELETED_NAME);
        $currentSite = $this->services->getSiteService()->find($currentSiteId);        
        $result = [
            'form' => $form,
            'site' => $currentSite,
            'querySiteId' => $siteId
        ];
        if ($request->isPost()) {            
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $text = $form->get(SearchForm::TEXT_FIELD_NAME)->getValue();
                $text = trim($text);
                if (mb_strlen($text) >= 3) {
                    $siteIds = $allBranches ? null : [(int)$siteId];
                    $result['pages'] = $this->getPagesTable($text, $siteIds, $withDeleted ? null : false, null, null, 1, 10);
                    $result['users'] = $this->getUsersTable($text, $siteIds, null, null, 1, 10);                    
                    $pageCount = $result['pages']->getPaginator()->getTotalItemCount();
                    $userCount = $result['users']->getPaginator()->getTotalItemCount();
                    if ($pageCount === 1 && $userCount === 0) {
                        $page = $result['pages']->getPaginator()->getItem(1);
                        return $this->redirect()->toUrl("/page/{$page->getId()}");
                    } elseif ($pageCount === 0 && $userCount === 1) {
                        $user = $result['users']->getPaginator()->getItem(1);
                        return $this->redirect()->toUrl("/user/{$user->getId()}");
                    }
                }                
            }
        }        
        return new ViewModel($result);
    }    

    public function apiFindUsersAction()
    {
        $result = ['users' => []];
        $site = null;
        $siteName = $this->params()->fromQuery('site', null);
        if ($siteName) {
            try {
                $site = $this->services->getSiteService()->findByShortName($siteName);
            } catch (\InvalidArgumentException $e) {
                $result['error'] = 'Invalid site';
                return new JsonModel($result);
            }        
        }
        $name = $this->params()->fromQuery('name', null);       
        if (!is_string($name) || strlen($name) > 256 || strlen($name) < 3) {
            $result['error'] = 'Name must be between 3 and 256 characters long';
            return new JsonModel($result);
        }        
        $limit = filter_var($this->params()->fromQuery('limit', 50), FILTER_VALIDATE_INT);
        if (!$limit || ($limit < 1) || ($limit > 50)) {
            $limit = 50;
        }        
        if ($site) {
            $users = $this->services->getUserService()->findUsersOfSiteByName($site->getId(), $name, null, true);
        } else {
            $users = $this->services->getUserService()->findByName($name, null, true);
        }        
        $users->setItemCountPerPage($limit);
        foreach ($users as $user) {
            $result['users'][] = $user->toArray();
        }
        return new JsonModel($result);        
    }

    public function apiFindPagesAction()
    {
        $result = ['pages' => []];
        $siteName = $this->params()->fromQuery('site', 'en');        
        try {
            $site = $this->services->getSiteService()->findByShortName($siteName);
        } catch (\InvalidArgumentException $e) {
            $result['error'] = 'Invalid site';
            return new JsonModel($result);
        }        
        $title = $this->params()->fromQuery('title', null);       
        if (!is_string($title) || strlen($title) > 256 || strlen($title) < 3) {
            $result['error'] = 'Title must be between 3 and 256 characters long';
            return new JsonModel($result);
        }         
        $limit = filter_var($this->params()->fromQuery('limit', 50), FILTER_VALIDATE_INT);
        if (!$limit || ($limit < 1) || ($limit > 50)) {
            $limit = 50;
        }
        $randomize = $this->params()->fromQuery('random', 0);        
        if ($randomize) {
            $orderBy = 'random';
        } else {
            $orderBy = null; // [DbViewPages::CLEANRATING => Order::DESCENDING);
        }
        $pages = $this->services->getPageService()->findByName($title, [$site->getId()], false, $orderBy, true, 1, $limit);
        foreach ($pages as $page) {
            $result['pages'][] = $page->toArray();
        }
        return new JsonModel($result);        
    }    

    public function pageListAction()
    {
        $result = ['success' => false];
        $siteId = $this->params()->fromQuery('siteId', $this->services->getUtilityService()->getSiteId());
        $page = (int)$this->params()->fromQuery('page', 1);
        $perPage = (int)$this->params()->fromQuery('perPage', 10);
        $orderBy = $this->params()->fromQuery('orderBy', null);
        $order = $this->params()->fromQuery('ascending', null);
        $query = $this->params()->fromQuery('query', '');
        $allBranches = (bool) $this->params()->fromQuery('allBranches', false);
        $withDeleted = (bool) $this->params()->fromQuery('withDeleted', false);        
        if ($order !== null) {
            if ($order) {
                $order = Order::ASCENDING;
            } else {
                $order = Order::DESCENDING;
            }
        }
        $siteIds = $allBranches ? null : [(int)$siteId];
        $table = $this->getPagesTable($query, $siteIds, $withDeleted ? null : false, $orderBy, $order, $page, $perPage);
        $renderer = $this->getServiceLocator()->get('ViewHelperManager')->get('partial');
        if ($renderer) {
            $result['success'] = true;                
            $result['content'] = $renderer(
                'partial/tables/default/table.phtml', 
                [
                    'table' => $table, 
                    'data' => ['siteIds' => $siteIds]
                ]
            );
        }
        return new JsonModel($result);        
    }
    
    public function userListAction()
    {
        $result = ['success' => false];
        $siteId = (int)$this->params()->fromQuery('siteId', $this->services->getUtilityService()->getSiteId());        
        $page = (int)$this->params()->fromQuery('page', 1);
        $perPage = (int)$this->params()->fromQuery('perPage', 10);
        $orderBy = $this->params()->fromQuery('orderBy', null);
        $order = $this->params()->fromQuery('ascending', null);
        $query = $this->params()->fromQuery('query', '');
        if ($order !== null) {
            if ($order) {
                $order = Order::ASCENDING;
            } else {
                $order = Order::DESCENDING;
            }
        }
        $allBranches = (bool) $this->params()->fromQuery('allBranches', false);        
        $table = $this->getUsersTable($query, $allBranches ? null : $siteId, $orderBy, $order, $page, $perPage);
        $renderer = $this->getServiceLocator()->get('ViewHelperManager')->get('partial');
        if ($renderer) {
            $result['success'] = true;                
            $result['content'] = $renderer(
                'partial/tables/default/table.phtml', 
                [
                    'table' => $table, 
                    'data' => ['siteId' => $siteId]
                ]
            );
        }
        return new JsonModel($result);        
    }
    
    public function autocompleteAction()
    {
        $maxItems = 3;
        $result = ['success' => true];
        $siteId = (int)$this->params()->fromQuery('siteId', $this->services->getUtilityService()->getSiteId());
        $query = $this->params()->fromQuery('query', '');
        $pages = $this->services->getPageService()->findByName($query, [$siteId], false, null, true, 1, $maxItems);
        $users = $this->services->getUserService()->findUsersOfSiteByName($siteId, $query, null, true);
        $result['pages'] = [];
        $i = 1;        
        foreach ($pages as $page) {
            $result['pages'][] = [
                'id' => $page->getId(),
                'label' => $page->getTitle(),
                'altTitle' => $page->getAltTitle()            
            ];
            $i++;
            if ($i > $maxItems) {
                break;
            }
        }
        $result['users'] = [];
        $i = 1;
        foreach ($users as $user) {
            $result['users'][] = [
                'id' => $user->getId(),
                'label' => $user->getDisplayName()];
            $i++;
            if ($i > $maxItems) {
                break;
            }            
        }
        return new JsonModel($result);
    }    
}
