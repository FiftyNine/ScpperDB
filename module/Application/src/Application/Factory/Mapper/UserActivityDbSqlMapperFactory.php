<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Factory\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Utils\DbConsts\DbViewUserActivity;
use Application\Mapper\UserActivityDbSqlMapper;
use Application\Hydrator\UserActivityDbHydrator;

/**
 * Description of UserActivityDbSqlMapperFactory
 *
 * @author Alexander
 */
class UserActivityDbSqlMapperFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
        $hydrator = new UserActivityDbHydrator();
        $prototype = $serviceLocator->get('UserActivityPrototype');
        return new UserActivityDbSqlMapper($dbAdapter, $hydrator, $prototype, DbViewUserActivity::TABLE, '');                        
    }
}
