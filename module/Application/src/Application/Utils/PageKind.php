<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Utils;

/**
 * Description of PageKind
 *
 * @author Alexander
 */
class PageKind
{
    const UNKNOWN = 0;
    const SCP = 1;
    const TALE = 2;
    const JOKE = 3;
    const ART = 4;
    const GOI = 5;
    const OTHER = 6;
    const SERVICE = 7;
    const ESSAY = 8;
    const AUDIO = 9;
    const AUTHOR_PAGE = 10;
    
    const DESCRIPTIONS = [
        self::UNKNOWN => 'Unknown',
        self::SCP => 'SCP',
        self::TALE => 'Tale',
        self::JOKE => 'Joke',
        self::ART => 'Art',
        self::GOI => 'GOI',
        self::OTHER => 'Other',
        self::SERVICE => 'Service',
        self::ESSAY => 'Essay',
        self::AUDIO => 'Audio',
        self::AUTHOR_PAGE => "Author's page"];
    
    /**
     * Get description for page kind
     * @param int $kind
     * @throws \InvalidArgumentException
     */
    static public function getDescription($kind)
    {
        $intKind = (int)$kind;
        if (array_key_exists($intKind, self::DESCRIPTIONS)) {
            return self::DESCRIPTIONS[$intKind];
        } else {
            throw new \InvalidArgumentException("Unknown page kind - $kind");
        }                
    }    

    /**
     * Get kindId by description
     * @param string $kind
     * @throws \InvalidArgumentException
     */
    static public function getIdByDescription($kind)
    {
        foreach (self::DESCRIPTIONS as $id => $desc) {
            if ($desc === $kind) {
                return $id;
            }            
        }
        throw new \InvalidArgumentException("Unknown page kind - $kind");
    }
    
}
