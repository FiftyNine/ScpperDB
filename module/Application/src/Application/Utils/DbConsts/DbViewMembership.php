<?php

namespace Application\Utils\DbConsts;

class DbViewMembership
{
    const TABLE = 'view_membership';
    const __ID = '__Id';
    const SITEID = 'SiteId';
    const SITENATIVENAME = 'SiteNativeName';
    const SITE = 'Site';
    const SITEENGLISHNAME = 'SiteEnglishName';
    const USERID = 'UserId';
    const USERNAME = 'UserName';
    const DISPLAYNAME = 'DisplayName';
    const JOINDATE = 'JoinDate';
    const LASTACTIVITY = 'LastActivity';
    const VOTES = 'Votes';
    const REVISIONS = 'Revisions';
    const PAGES = 'Pages';


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
