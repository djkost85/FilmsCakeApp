<?php
/**
 * CakePHP User Model file
 * 
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    0.9.0
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */

App::uses('AuthComponent', 'Controller/Component');

/**
 * User model (MVC component)
 *
 * Users for authentication purposes. Access to this app can only
 * be done as a User. ACL-set rights are based on this model.
 *
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    Release: @package_version@
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */
class User extends AppModel
{

    /**
     *  CakePHP: before any save
     *
     * @param   array $options      array containing the options for the save action
     * @return  boolean  true       if the operation should continue, false if it should abort
     * @see     /lib/Cake/Model/Model.php
     */
    public function beforeSave($options = array())
    {
        if (isset($this->data[$this->alias]['password'])) {
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
        }
        return true;
    }
    
    /**
     *  CakePHP: validation scheme
     *  
     *  @var  array
     */
    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A username is required'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'on' => 'create',
                'message' => 'A password is required'
            )
        ),
        'role' => array(
            'valid' => array(
                'rule' => array('inList', array('editor', 'viewer', 'guest')),
                'message' => 'Please enter a valid role',
                'allowEmpty' => false
            )
        )
    );
}
