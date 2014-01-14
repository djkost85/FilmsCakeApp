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

// cakephp doesn't support namespaces
//namespace ImdbData;
//use ImdbData\RawWebData\RawWebDataHtml;
//use ImdbData\ImdbDataInterpreter\ImdbDataInterpreterHtml;

// cakephp including
App::uses('ImdbData', 'ImdbData');
// HTML data
App::uses('RawWebDataHtml', 'ImdbData/RawWebData');
App::uses('ImdbDataInterpreterHtml', 'ImdbData/ImdbDataInterpreter');

/**
 * Construction logic for ImdbData
 * 
 * @package     ImdbData
 * @todo        Consider turning into abstract factory with subclassing to pick the type...
 */
class ImdbDataFactory
{
    /**
     * Creates HTML version of ImdbData
     * 
     * @param  string $imdbCode
     * @return ImdbData
     */
    public static function createImdbDataHtml($imdbCode)
    {
       $imdbCode = self::_cleanImdbCode($imdbCode);

        return new ImdbData($imdbCode, new RawWebDataHtml($imdbCode), new ImdbDataInterpreterHtml());
    }

    /**
     * Builds JSON / IMDbAPI version of ImdbData
     * 
     * @param  string $imdbCode
     * @return ImdbData
     */
    /*
    public static function buildImdbDataJson($imdbCode)
    {
       $imdbCode = self::_cleanImdbCode($imdbCode);

        return new ImdbData($imdbCode, new RawWebDataInterface RawWebDataJson($imdbCode),
            new ImdbDataInterpreterInterface ImdbDataInterpreterJson());
    }
    */

    /**
     * Cleans up IMDb Code
     * @param  string $imdbCode     unclean/unchecked string with imdb code
     * @return string               clean imdb code
     */
    private static function _cleanImdbCode($imdbCode)
    {
        $imdbCode = trim($imdbCode);

        // strip tt if present, we only want the numerical part
        if (preg_match('/^tt(.*)$/i', $imdbCode, $m)) {
            $imdbCode = trim($m[1]);
        }

        // prepend 0's if code shorter than 7 characters
        $imdbCode = sprintf('%07s', $imdbCode);

        return $imdbCode;
    }
}