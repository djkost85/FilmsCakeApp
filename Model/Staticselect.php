<?php
/**
 * CakePHP Staticselect Model file
 * 
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    0.9.0
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */

App::uses('AppModel', 'Model');

/**
 * Staticselect model (pseudo MVC component)
 *
 * Container pseudomodel for using static select lists in forms
 * and for validating input for fields that should store the 
 * types made available by this model.
 *
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    Release: @package_version@
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */
class Staticselect extends AppModel
{
    /**
     * CakePHP: don't use a database table for this model.
     *
     * @var boolean
     */
    var $useTable = false;

    /**
     * Option list for Viewing types
     *
     * @param   bool $addEmpty      if true, adds an empty field as the first list option
     * @return  array               array containing the options to display in $key => $display pairs
     */
    function viewingTypes($addEmpty = false)
    {
        $options = array();

        if ( $addEmpty )
        {
            $options = array('' => '');
        }

        // list of options -- note that they should stay ordered from highest to lowest quality
        $options = array_merge(
            $options,
            array(
                'bestbios' => 'Arthouse / Filmhuis',
                'bios' =>     'Movie Theatre',
                'dvd' =>      'DVD quality',
                'tv' =>       'TV quality',
                'avi' =>      'AVI / compressed',
                'inet' =>     'Internet Streamed',
            )
        );
        return $options;
    }

    /**
     * Option list for Viewing types, short version (for list views)
     * 
     * @return  array               array containing the options to display in $key => $display pairs
     */
    function viewingTypesShort()
    {
        $options = array(
            'bestbios' => 'Arthouse',
            'bios' =>     'Theatre',
            'dvd' =>      'DVD',
            'tv' =>       'TV',
            'avi' =>      'AVI',
            'inet' =>     'Internet',
        );
        return $options;
    }

    /**
     * Gives a rank number that rates the given viewingType from 0 (low quality)
     * up (for higher quality).
     * 
     * @param  string $type     string indicating the viewingType
     * @return int              the viewing type rank
     */
    function viewingTypeRank($type)
    {
        // loop through the available types, remembering their order
        $count = count(Staticselect::viewingTypes()) + 1;
        foreach(Staticselect::viewingTypes() as $key => $listType) {
            if ($type === $key) {
                return $count;
            }
            $count--;
        }

        return 0;
    }

    /**
     * Lists options statuses for Films
     *
     * @return  array               array containing the options to display in $key => $display pairs
     */
    function filmStatuses()
    {
        $options = array(
            Configure::read('filmStatuses.notseen') =>      'Not Seen',
            Configure::read('filmStatuses.seen') =>         'Seen',
            Configure::read('filmStatuses.partial') =>      'Partially Seen',
        );
        return $options;
    }

    /**
     * Lists options for statuses for IMDb data for Film
     *
     * @param   bool $addEmpty      if true, adds an empty field as the first list option
     * @return  array               array containing the options to display in $key => $display pairs
     */
    function imdbStatuses($addEmpty = false)
    {
        $options = array();

        if ($addEmpty) {
            $options = array( '' => '' );
        }

        $options = array_merge(
            $options,
            array(
                Configure::read('imdbStatuses.nodata') =>       'No data',
                Configure::read('imdbStatuses.partialdata') =>  'Partial data',
                Configure::read('imdbStatuses.fulldata') =>     'Full data',
                Configure::read('imdbStatuses.error') =>        'Error',
            )
        );
        return $options;
    }
}
