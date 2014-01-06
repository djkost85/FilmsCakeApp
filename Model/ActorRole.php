<?php
/**
 * CakePHP ActorRole Model file
 * 
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    0.9.0
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */
   
App::uses('AppModel', 'Model');

/**
 * ActorRole model (MVC component)
 *
 * ActorRole connects Film with Actor, providing the role they played
 * in the Film. This may be a 'null'-value, depending on what IMDb
 * provides.
 *
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    Release: @package_version@
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */
class ActorRole extends AppModel
{
    /**
     * CakePHP: belongsTo associations
     * 
     * @var array
     */
    public $belongsTo = array(
        'Film' => array(
            'order' => 'Film.year DESC, Film.title ASC',
        ),
        'Actor' => array(
            'order' => 'Actor.name ASC',
        ),
    );


    /**
     * CakePHP: makes changes before a save action:
     * Truncates role where necessary.
     *
     * @param   array $options  options for the saving process
     * @return  boolean         the save will only proceed if this returns true
     */
    public function beforeSave($options = array())
    {
        // if role is > 99, truncate

        if (!empty($this->data['ActorRole']['role']) && strlen($this->data['ActorRole']['role']) > 99) {
            $this->data['ActorRole']['role'] = substr($this->data['ActorRole']['role'], 0, 99);
        }

        return true;
    }
}