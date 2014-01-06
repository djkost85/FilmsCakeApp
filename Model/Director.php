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
 * Director model (MVC component)
 *
 * Directors are IMDb-read directors connected to films. They are
 * generally not user-set, but may be edited -- and should be edited.
 * to get the right last/first name split.
 *
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    Release: @package_version@
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */
class Director extends AppModel
{
    
    /**
     * CakePHP: Validation rules
     *
     * @var  array
     */
    public $validate = array(
        'last_name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
    ); 

    /**
     * CakePHP: hasAndBelongsToMany associations
     * 
     * @var  array
     */
    public $hasAndBelongsToMany = array(
        'Film' => array(
            'className' => 'Film',
            'joinTable' => 'directors_films',
            'foreignKey' => 'director_id',
            'associationForeignKey' => 'film_id',
            'unique' => 'keepExisting',
            'conditions' => '',
            'fields' => '',
            'order' => array('Film.year DESC', 'Film.title ASC'),
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => ''
        )
    );

    /**
     * CakePHP: virtual fields
     *
     * @var array
     */
    public $virtualFields = array(
        'film_count' => 'SELECT COUNT(*) FROM directors_films as FilmDirector WHERE FilmDirector.director_id = Director.id',
    );

}