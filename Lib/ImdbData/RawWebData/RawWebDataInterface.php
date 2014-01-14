<?php
/**
 * ImdbData retrieval tools
 *
 * @package    ImdbData
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    Release: @package_version@
 * @link       http://www.github.com/CJZimmerman/ImdbData
 */

// cakephp doesn't support namespace
//namespace ImdbData\RawWebData;

/**
 * RawWebData interface
 * @package    ImdbData
 */
interface RawWebDataInterface
{
    /**
     * Retrieves raw data from the web, for a given movie code
     * @param  string $imdbCode     IMDb code (minus the 'tt')
     * @return boolean      true if lookup was succesful
     */
    public function getData($imdbCode);
}