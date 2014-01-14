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
//namespace ImdbData\RawWebData;

/**
 * HTML version of RawWebData
 * @package     ImdbData
 */
class RawWebDataHtml implements RawWebDataInterface
{
    /**
     * @param  string $imdbCode
     * @return array        associative, with HTML data for 'main', 'cast' and 'trivia'
     */
    public function getData($imdbCode)
    {
        $data = array(
            'main' => null,
            'cast' => null,
            'trivia' => null,
        );

        $urlBase = "http://www.imdb.com/title/tt$imdbCode/";

        // read data from the IMDb website: main, cast and trivia
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'header'=> "Content-type: application/xhtml+xml\n"
                    . "Connection: keep-alive\n"
                    . "Accept: text/html,application/xhtml+xml,application/xml",
                'user_agent' => 'Mozilla/5.0 (Windows NT 6.1; rv:26.0) Gecko/20100101 Firefox/26.0',
            )
        ));

        // main
        $result = file_get_contents($urlBase, false, $context);
        if (!empty($result)) {
            $data['main'] = $result;
        }

        // cast
        $result = file_get_contents($urlBase . "fullcredits", false, $context);
        if (!empty($result)) {
            $data['cast'] = $result;
        }

        // trivia
        $result = file_get_contents($urlBase . "trivia", false, $context);
        if (!empty($result)) {
            $data['trivia'] = $result;
        }

        return $data;
    }
}