<?php
/**
 * Retrieving IMDb data CakePHP.
 *
 * @package    FilmsCakeApp
 * @subpackage Imdb
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    0.9.0
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */

App::import('Vendor', 'Curl', array('file' => 'Curl.php'));

/**
 * ImdbRetrieve
 *
 * Interpreter of IMDb.com data; provides array with all relevant
 * IMDb data to be found for a movie with a given IMDb-code (numerical,
 * including the 0's, but without 'tt').
 *
 * @package    FilmsCakeApp
 * @subpackage Imdb
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    Release: @package_version@
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */
class ImdbRetrieve
{
    /**
     * Retrieves data from the IMDb website.
     *
     * @param  string $imdb     string with the numerical imdb code for the film (not including 'tt')
     * @return array            array with data that could be retrieved from the IMDb page for the film
     * @todo   fix proxy handling, make configurable
     */
    public function getData($imdb = null)
    {
        $data = array();
        $imdb = trim($imdb);
        
        $curl = new Curl();

        // pretend we're a normal browser
        $curl->setUserAgent('Mozilla/5.0 (Windows; U; Windows NT 6.1; en-GB; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8');

        // if possible, set a proxy
        if (method_exists($curl, 'setProxy')) {
            // custom proxy
            // TO DO: make this configurable
            //$curl->setProxy( '202.98.123.126:8080', 8080 );
        }
        
        /*
            1. get overview
            2. get full cast
                2a. remember who gets 'first billed' rank
            3. get trivia
         */

        // get page and analyze data
        $curl->get('http://www.imdb.com/title/tt' . $imdb . '/');

        if ($curl->error) {
            $data['error'] = $curl->error_message;
            return $data;
        }

        if ($curl->http_error || $curl->http_status_code != 200) {
            $data['error'] = __('http status code is %s.', $curl->http_status_code);
            return $data;
        }

        $data = $this->_analyzeData('main', $curl->response);

        // full cast
        $curl->get('http://www.imdb.com/title/tt' . $imdb . '/fullcredits');

        if ($curl->error) {
            $data['error_cast'] = $curl->error_message;
        } else if ($curl->http_error || $curl->http_status_code != 200) {
            $data['error_cast'] = __('http status code is %s.', $curl->http_status_code);
        } else {
            $data['cast'] = $this->_analyzeData('cast', $curl->response);
        }

        // apply and remove temporary first_billed data
        if (empty($data['error_cast']) && !empty($data['cast']['actors'])) {
            foreach ($data['first_billed'] as $tt => $order) {
                // need to cast the tt value to string, is stored as int if first char isn't "0"
                $tt = (string) $tt;
                foreach ($data['cast']['actors'] as $key => $act) {
                    if ($tt === $act['id']) {
                        $data['cast']['actors'][$key]['bill_order'] = $order;
                    }
                }
            }
        }
        unset($data['first_billed']);
       
        // trivia
        $curl->get('http://www.imdb.com/title/tt' . $imdb . '/trivia');

        if ($curl->error) {
            $data['error_trivia'] = $curl->error_message;
        } else if ($curl->http_error || $curl->http_status_code != 200) {
            $data['error_trivia'] = __('http status code is %s.', $curl->http_status_code);
        } else {
            $data['trivia' ] = $this->_analyzeData('trivia', $curl->response);
        }

        return $data;
    }
    
    /**
     * Analyzes IMDb HTML data; gets all relevant IMDb information out of the
     * web page's HTML. This fucntion should be updated to match IMDb the format
     * that IMDb's HTML takes.
     * 
     * @param  string $dataset  which type of page (main (default), cast, trivia)
     * @param  string $html     IMDb web page
     * @return array            array with data retrieved
     */
    private function _analyzeData($dataset, $html)
    {
        $data = array();

        switch ($dataset) {

            case 'cast':
                // directors
                if (preg_match( '/<h4.+?>\s*?Directed by.*?<\/h4>(.+?)<\/table>/ims', $html, $m)) {
                    if (preg_match_all('/\/name\/nm([0-9]+)\/.+?>(.+?)<\/a>/ims', $m[1], $m)) {
                        $data['directors'] = array();
                        foreach ($m[1] as $key => $dir) {
                            $nameParts = ImdbRetrieve::getFirstAndLastName(trim($m[2][$key]));

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
                    if (preg_match_all(
                            '/<tr.*?>.*?<td class="itemprop".*?>.*?<a href="\/name\/nm([0-9]+)\/.+?>(.+?)<\/a>.*?'
                            .   '<td class="character">(.+?)<\/td>.*?<\/tr>/ims',
                            $m[1], $m
                        )
                    ) {
                        $data['actors'] = array();
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
                            $char = preg_replace('/à/', 'a', $char);

                            $nameParts = ImdbRetrieve::getFirstAndLastName(trim($name));

                            $data['actors'][] = array(
                                'id' =>         $act,
                                'name' =>       trim($name),
                                'last_name' =>  $nameParts['last_name'],
                                'first_name' => $nameParts['first_name'],
                                'role' =>       preg_replace("/(\s+)/ims", ' ', trim($char)),
                            );
                        }
                    }
                }
                break;


            case 'trivia':
                // get trivia blocks
                if (preg_match('/<div id="trivia_content"[^>]*>(.*)<div class="article"/ims', $html, $m)) {
                    if (preg_match_all('/<div id="tr[0-9]+".+?<div class="sodatext"[^>]*>(.+?)<\/div/ims', $m[1], $m)) {
                        $data = array();
                        foreach ($m[1] as $triv) {
                            $data[] = trim($triv);
                        }
                    }
                }
                break;


            default:
                // get title, year (from head title)
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
        }

        return $data;
    }

    /**
     * Analyzes a full name and returns the most likely first/last name split.
     * 
     * @param  string $name     the name to be analyzed/split
     * @return array            array with 'first_name' and 'last_name' keys (may both be null)
     */
    public function getFirstAndLastName($name)
    {
        $return = array(
            'first_name' => null,
            'last_name' => null,
        );

        if (empty($name) || strlen($name) === 0) {
            return $return;
        }

        // match any FIRSTNAME LASTNAME (II) type of name
        // any FIRST MIDDLE LASTNAME format treats the second+ names as part of the last name
        if (    preg_match('/^([^ ]+) ([^ ]+( [^ ]+)*) \([ivx]+\)$/i', $name, $m)
            ||  preg_match('/^([^ ]+) ([^ ]+( [^ ]+)*)$/i', $name, $m)
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
            $return['last_name'] = $name;
        }

        return $return;
    }
}