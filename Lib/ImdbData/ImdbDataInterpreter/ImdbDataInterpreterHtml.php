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
 * HTML version of ImdbDataInterpreter
 * 
 * @package     ImdbData
 */
class ImdbDataInterpreterHtml implements ImdbDataInterpreterInterface
{
    /**
     * @param  array $rawData   associative array with 'main','cast' and 'trivia' html strings
     * @return array    returns false if data could not be interpreted
     * @todo add exception for incorrect parameters, throw that when encountered
     */
    public function interpretData($rawData)
    {
        $data = array();

        // check if rawData is an array
        if (empty($rawData) || !is_array($rawData)) {
            return false;
        }

        // interpret main
        if (isset($rawData['main'])) {
            $data = array_merge($data, $this->_interpretHtmlMain($rawData['main']));
        }

        // interpret cast: if we have first_billed data, provide it
        if (isset($rawData['cast'])) {
            $data = array_merge($data, $this->_interpretHtmlCast($rawData['cast'],
                isset($data['first_billed']) ? $data['first_billed'] : null));

            // clean up first billed information (only needed for interpreting cast)
            if (isset($data['first_billed'])) {
                unset($data['first_billed']);
            }
        }

        // interpret trivia
        if (isset($rawData['trivia'])) {
            $data = array_merge($data, $this->_interpretHtmlTrivia($rawData['trivia']));
        }
        
        return $data;
    }


    /**
     * @param  string $html
     * @return array
     */
    private function _interpretHtmlMain($html) {
        $data = array();

        // get title, year (from head title) -- this is the 'localized' title (likely to be the American one)
        if (preg_match('/<title>\s*(IMDb - )*(.*?)\s*(\(([0-9]{4})\))*( - IMDb)*\s*<\/title>/ims', $html, $m)) {
            $data['title'] = $m[2];
            $data['year'] =  $m[4];
        }

        // if it exists, get the original title
        if (preg_match('/<span class="title-extra"[^>]*>\s*"([^"]+)"\s*<[^>]+>\(original title\)<\/[^>]+>/ims',
            $html, $m)
        ) {
            $data['title'] = $m[1];
        }

        // get rating
        if (preg_match('/<div class=".+?star-box-giga-star.+?>.*?([0-9]+(\.[0-9]+)*).*?<\//ims', $html, $m)) {
            $data['rating'] = trim( $m[1] );
        } else if (preg_match('/<div class="rating.+?title=".+?([0-9]+(\.[0-9]+)*)\/10/ims', $html, $m)) {
            $data['rating'] = trim( $m[1] );
        }

        // get duration
        if (preg_match('/<time.+?>\s*?([0-9]+).+?<\/time>/ims', $html, $m)) {
            $data['duration'] = trim( $m[1] );
        }

        // get genres
        if (preg_match_all('/href="\/genre\/(.+)\?ref/im', $html, $m)) {
            $data['genres'] = array();
            foreach ($m[1] as $genre) {
                $data['genres'][] = trim(strtolower($genre));
            }
            $data['genres'] = array_unique( $data['genres'] );
        }
        
        // get countries
        if (preg_match_all('/href="\/country\/.+>(.+)</im', $html, $m)) {
            $data['countries'] = array();
            foreach ($m[1] as $country) {
                $data['countries'][] = trim( $country );
            }
            $data['countries'] = array_unique( $data['countries'] );
        }

        // get languages
        if (preg_match_all('/href="\/language\/.+>(.+)</im', $html, $m)) {
            $data['languages'] = array();
            foreach ($m[1] as $lang) {
                $data['languages'][] = trim( $lang );
            }
            $data['languages'] = array_unique( $data['languages'] );
        }

        // get colors
        if (preg_match_all('/href="\/search\/.+?colors=.+>(.+)</im', $html, $m)) {
            $data['colors'] = array();
            foreach ($m[1] as $clr) {
                $data['colors'][] = trim(strtolower($clr));
            }
            $data['colors'] = array_unique( $data['colors'] );
        }

        // get first billed actors (store their order)
        $data['first_billed'] = array();

        if (preg_match('/<table\sclass="cast_list".*?>(.+?)<\/table>/ims', $html, $m)) {
            if (preg_match_all(
                    '/<tr.*?>.*?<td class="itemprop".*?>.*?<a href="\/name\/nm([0-9]+)\/.+?>(.+?)<\/a>.*?'
                    .   '<td class="character">(.+?)<\/td>.*?<\/tr>/ims',
                    $m[1], $m
                )
            ) {
                $count = 0;

                foreach ($m[1] as $act) {
                    $count++;
                    $data['first_billed'][$act] = $count;
                }
            }
        }

        return $data;
    }

    /**
     * @param  string $html
     * @param  array  $firstBilled      associative array with imdb nm ids, storing bill order (null if unknown)
     * @return array
     */
    private function _interpretHtmlCast($html, $firstBilled = null) {
        $data = array();
        
        // directors
        if (preg_match( '/<h4.+?>\s*?Directed by.*?<\/h4>(.+?)<\/table>/ims', $html, $m)) {
            if (preg_match_all('/\/name\/nm([0-9]+)\/.+?>(.+?)<\/a>/ims', $m[1], $m)) {
                $data['directors'] = array();
                foreach ($m[1] as $key => $dir) {
                    $nameParts = self::_getFirstAndLastName(trim($m[2][$key]));

                    $data['directors'][] = array(
                        'id' =>         $dir,
                        'name' =>       trim($m[2][$key]),
                        'last_name' =>  $nameParts['last_name'],
                        'first_name' => $nameParts['first_name'],
                    );
                }
            }
        }

        // cast
        if (preg_match('/<table.*?class="cast_list".*?>(.*)<\/table>/ims', $html, $m)) {
            $tmp = $m[1];

            debug(preg_match_all(
                    '/<tr.*?>.*?<td class="itemprop".*?>.*?<a href="\/name\/nm([0-9]+)\/.+?>(.+?)<\/a>.*?'
                    .   '<td class="character">(.+?)<\/td>.*?<\/tr>/ims',
                    $tmp, $m
                ));
            debug(count($m[1]));

            // needs the count check on $m[1] to avoid preg_match_all == false even though matches were found...
            if (preg_match_all(
                    '/<tr.*?>.*?<td class="itemprop".*?>.*?<a href="\/name\/nm([0-9]+)\/.+?>(.+?)<\/a>.*?'
                    .   '<td class="character">(.+?)<\/td>.*?<\/tr>/ims',
                    $tmp, $m)
                || count($m[1])
            ) {

                $data['cast'] = array();
                foreach ($m[1] as $key => $act) {
                    $name = null;
                    $char = null;

                    // clean up the name part
                    if (preg_match('/<span.*?>(.*)<\/.*?/ims', $m[2][$key], $ma)) {
                        $name = $ma[1];
                    }
                    
                    // clean up the character part
                    $char = $m[3][$key];

                    // below are 2 precise options, the one used is a catch-all for any html tag / closer
                    //$char = trim(preg_replace('/(<a href="\/character\/[^>]*>|<\/a>)/ims', '', $m[3][$key]));
                    //$char = trim(preg_replace('/(<div[^>]*>|<\/div>)/ims', '', $char));
                    $char = trim(preg_replace('/(<[^>]+>|<\/[^>]+>)/ims', '', $char));
                    
                    // remove weird character that causes problems (reason unknown)
                    // note: it is not a charset problem (UTF8 all the way)
                    $char = preg_replace('/à/', 'a', $char);

                    $nameParts = self::_getFirstAndLastName(trim($name));

                    $data['cast'][] = array(
                        'id' =>         $act,
                        'name' =>       trim($name),
                        'last_name' =>  $nameParts['last_name'],
                        'first_name' => $nameParts['first_name'],
                        'role' =>       preg_replace("/(\s+)/ims", ' ', trim($char)),
                    );
                }
            }
        }

        // apply bill order
        if (isset($data['cast']) && !empty($firstBilled) && is_array($firstBilled)) {
            foreach ($firstBilled as $tt => $order) {
                // need to cast the tt value to string, is stored as int if first char isn't "0"
                $tt = (string) $tt;
                foreach ($data['cast'] as $key => $act) {
                    if ($tt === $act['id']) {
                        $data['cast'][$key]['bill_order'] = $order;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param  string $html
     * @return array
     */
    private function _interpretHtmlTrivia($html) {
        $data = array();

        // get trivia blocks
        if (preg_match('/<div id="trivia_content"[^>]*>(.*)<div class="article"/ims', $html, $m)) {
            if (preg_match_all('/<div id="tr[0-9]+".+?<div class="sodatext"[^>]*>(.+?)<\/div/ims', $m[1], $m)) {
                $data['trivia'] = array();
                foreach ($m[1] as $triv) {
                    $data['trivia'][] = trim($triv);
                }
            }
        }
        
        return $data;
    }

    /**
     * Analyzes a full name and returns the most likely first/last name split.
     * 
     * @param  string $fullname
     * @return array            array with 'first_name' and 'last_name' keys (may both be null)
     */
    private static function _getFirstAndLastName($fullname)
    {
        $return = array(
            'first_name' => null,
            'last_name' => null,
        );

        if (empty($fullname) || strlen($fullname) === 0) {
            return $return;
        }

        // match any FIRSTNAME LASTNAME (II) type of name
        // any FIRST MIDDLE LASTNAME format treats the second+ names as part of the last name
        if (    preg_match('/^([^ ]+) ([^ ]+( [^ ]+)*) \([ivx]+\)$/i', $fullname, $m)
            ||  preg_match('/^([^ ]+) ([^ ]+( [^ ]+)*)$/i', $fullname, $m)
        ) {
            $return['first_name'] = $m[1];
            $return['last_name'] = $m[2];

            // catch initials for middle name, move them to first name
            if (preg_match('/^([a-z]\.) (.*)$/i', $return['last_name'], $m)) {
                $return['first_name'] .= ' ' . $m[1];
                $return['last_name'] = $m[2];
            }

        } else {
            // fallback: return entire name as last name
            $return['last_name'] = $fullname;
        }

        return $return;
    }
}
