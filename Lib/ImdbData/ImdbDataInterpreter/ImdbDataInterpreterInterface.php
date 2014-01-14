<?php
/**
 * ImdbData retrieval tools
 *
 * @package     ImdbData
 * @copyright   Copyright (c) 2013 Coen Zimmerman
 * @license     http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version     Release: @package_version@
 * @link        http://www.github.com/CJZimmerman/ImdbData
 */

// cakephp doesn't support namespace
//namespace ImdbData\ImdbDataInterpreter;

/**
 * ImdbDataInterpreter interface
 * @package     ImdbData
 */
interface ImdbDataInterpreterInterface
{
    /**
     * Interprets raw web data
     * @return array    array containing organized data that could be interpreted from the raw data
     */
    public function interpretData($rawData);
}