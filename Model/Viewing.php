<?php
/**
 * CakePHP Viewing Model file
 * 
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    0.9.0
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */

App::uses('AppModel', 'Model');

/**
 * Viewing model (MVC component)
 *
 * Viewings are each individual time a Film is watched. They store
 * the quality, date and (potentially) other circumstances.
 * 
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    Release: @package_version@
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */
class Viewing extends AppModel
{

    /**
     * CakePHP: $belongsTo
     *
     * @var array
     */
    public $belongsTo = array(
        'Film' => array(
            'counterCache' => true,     // keep track of the viewing_count in Films
        ),
    );

}