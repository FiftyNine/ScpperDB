<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Hydrator;

use Zend\Stdlib\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy;
use Application\Utils\DbConsts\DbViewMembership;
use Application\Utils\DbSelectColumns;

/**
 * Description of MembershipDbHydrator
 *
 * @author Alexander
 */
class MembershipDbHydrator extends PrefixDbHydrator
{
    public function __construct($prefix = '') {
        parent::__construct();
        $joinDateName = DbViewMembership::JOINDATE;
        if ($prefix && is_string($prefix)) {
            $map = $this->getPrefixedMap($prefix, DbSelectColumns::MEMBERSHIP);
            $this->setNamingStrategy(new MapNamingStrategy($map));
            $joinDateName = $prefix.'_'.$joinDateName;
        }        
        $this->addStrategy($joinDateName, new DateTimeFormatterStrategy('Y-m-d H:i:s'));
    }
}
