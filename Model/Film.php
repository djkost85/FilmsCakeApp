<?php
/**
 * CakePHP Film Model file
 * 
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    0.9.0
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */

App::uses('AppModel', 'Model');
App::uses('Staticselect', 'Model');
App::uses('CakeTime', 'Utility');

/**
 * Film model (MVC component)
 *
 * Main model type for this app. Films are partly user-set and partly
 * IMDb-important. Any aspect of them may be edited. The input state
 * is reflected in the imdb_status property.
 *
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    Release: @package_version@
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */
class Film extends AppModel
{
    /**
     * CakePHP: validation rules
     *
     * @var  array
     */
    public $validate = array(
        'title' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                //'message' => 'Your custom message here',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'year' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
        'seen_last' => array(
            'required' => array(
                'rule' => array('date'),
                'message' => 'Enter a valid date',
                'allowEmpty' => true,
            )
        ),
    );

    /**
     * CakePHP: hasAndBelongsToMany associations
     *
     * @var  array
     */
    public $hasAndBelongsToMany = array(
        'Director' => array(
            'className' => 'Director',
            'joinTable' => 'directors_films',
            'foreignKey' => 'film_id',
            'associationForeignKey' => 'director_id',
            'unique' => 'keepExisting',
            'conditions' => '',
            'fields' => '',
            'order' => 'Director.last_name ASC',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => '',
        ),
    );

    /**
     * CakePHP: hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'Viewing' => array(
            'className' => 'Viewing',
            'order' => 'Viewing.created DESC',
            'exclusive' => true,
            'dependent' => true,
        ),
        'ActorRole' => array(
            'unique' => 'keepExisting',     // this appears to do nothing, look up id's in the controller instead
            'order' => 'ActorRole.bill_order',
        ),
    );


    /**
     * CakePHP: saveAssociated (overload)
     *
     * Adds non-existant models if no 'id' is provided for HABTM relations
     *
     * @param   array $data     the data to save for associated models, CakePHP form-produced format
     * @return  mixed If atomic: True on success, or false on failure.
     * @see     /lib/Cake/Model/Model.php
     */
    public function saveAssociated($data = null, $options = array())
    {
        foreach ($data as $alias => $modelData) {
            if (!empty($this->hasAndBelongsToMany[$alias])) {

                $habtm = array();
                $Model = ClassRegistry::init($this->hasAndBelongsToMany[$alias]['className']);

                foreach ($modelData as $modelDatum) {

                    if (empty($modelDatum['id'])) {
                        $Model->create();
                    }

                    $Model->save($modelDatum);

                    if (empty($modelDatum['id'])) {
                        $habtm[] = $Model->getInsertID();
                    } else {
                        $habtm[] = $modelDatum['id'];
                    }
                }

                $data[$alias] = array($alias => $habtm);
            }
        }

        return parent::saveAssociated($data, $options);
    }


    /**
     * CakePHP: makes changes before a save action:
     * Determines (redundant) efficiency-helpers for last_seen date and best_quality
     * for a Film's viewings.
     *
     * @param   array $options  options for the saving process
     * @return  boolean         the save will only proceed if this returns true
     */
    public function beforeSave($options = array())
    {
        // check for viewings, if any,
        // set the seen_last date for film
        // set the best quality
        
        if (!empty($this->data['Viewing'])) {
            $last_seen = null;
            $best_quality = null;

            foreach ($this->data['Viewing'] as $viewing) {
                // date
                if (    $last_seen === null
                    ||  CakeTime::fromString($viewing['Viewing']['date']) > CakeTime::fromString($last_seen)
                ) {
                    $last_seen = $viewing['Viewing']['date'];
                }

                // quality
                if (    $best_quality === null
                    ||  Staticselect::viewingTypeRank($viewing['Viewing']['type']) >
                            Staticselect::viewingTypeRank($best_quality)
                ) {
                    $best_quality = $viewing['Viewing']['type'];
                }

                $this->data['Film']['seen_last'] = $last_seen;
                $this->data['Film']['best_quality'] = $best_quality;
            }
        }

        return true;
    }

}