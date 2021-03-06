<?php

namespace Application\Utils\DbConsts;

class DbCategories
{
    const TABLE = 'categories';
    const __ID = '__Id';
    const WIKIDOTID = 'WikidotId';
    const NAME = 'Name';
    const SITEID = 'SiteId';


    static public function hasField($field) 
    {
        if (!is_string($field)) {
            return false;
        }
        $field = strtoupper($field);
        $reflect = new \ReflectionClass(__CLASS__);
        foreach ($reflect->getConstants() as $name => $value) {
            if (strtoupper($value) === $field) {
                return true;
            }
        };
        return false;
    }
}
