<?php
/**
 * CakePHP Actor Model file
 * 
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    0.9.0
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */
   
App::uses('AppModel', 'Model');

/**
 * Actor model (MVC component)
 *
 * Actors are IMDb-read actors connected to films. They are not
 * user-set, but may be edited. This model is subject to change
 * to possibly support defining 'starring' roles and more details.
 *
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    Release: @package_version@
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */
class Actor extends AppModel
{

    /**
     * CakePHP: Validation rules
     * 
     * @var array
     */
    public $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
    );

    /**
     * CakePHP: hasMany associations
     * 
     * @var array
     */
    public $hasMany = array(
        'ActorRole' => array(
            'unique' => 'keepExisting',
        ),
    );

    /**
     * CakePHP: virtual fields
     *
     * @var array
     */
    public $virtualFields = array(
        'actorrole_count' => 'SELECT COUNT(*) FROM actor_roles as ActorRole WHERE ActorRole.actor_id = Actor.id',
    );
}