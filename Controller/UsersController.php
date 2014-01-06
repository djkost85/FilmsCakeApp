<?php
/**
 * User controller for CakePHP.
 *
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    0.9.0
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */

/**
 * User controller (MVC component)
 *
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    Release: @package_version@
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */
class UsersController extends AppController
{
    
    /**
     * CakePHP: isAuthorized -- Check if user can access this part of the site
     *
     * @param   array $user         user data for currently logged in user, if any
     * @return  boolean             true if the user is allowed access
     */
    public function isAuthorized($user = null)
    {
        // check Acl: anyone with viewing rights to whether user has the right role to see anything
        if ($this->Acl->check($user['role'], 'Users')) {
            return true;
        }

        return parent::isAuthorized($user);
    }

    /**
     * Action: Logs a user into the system
     *
     * @return  mixed  (see /lib/Cake/Controller/Controller.php)
     */
    public function login()
    {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                return $this->redirect($this->Auth->redirect());
            }
            $this->Session->setFlash(__('Invalid username or password, try again'));
        }
    }

    /**
     * Action: Logs the current user out of the system
     * 
     * @return  mixed  (see /lib/Cake/Controller/Controller.php)
     */
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }
    
    
    /**
     * Action: index -- displays a list of users
     * 
     * @return  mixed  (see /lib/Cake/Controller/Controller.php)
     */
    public function index()
    {
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    /**
     *  Action: view
     *
     * @return  mixed (see /lib/Cake/Controller/Controller.php)
     * @throws  NotFoundException if $id does not match an existing object
     */
    public function view($id = null)
    {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('user', $this->User->read(null, $id));
    }


    /**
     *  Action: add
     *
     *  Add a new user to the system.
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
        }
    }

    /**
     * Action: Edits a user
     *
     * @param   mixed $id       the id code of the object
     * @return  mixed  (see /lib/Cake/Controller/Controller.php)
     * @throws  NotFoundException if $id does not match an existing object
     */
    public function edit($id = null)
    {
        $this->User->id = $id;

        // nonexistant user: error
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }

        // posted form: save the user and go back to the user list
        if (    $this->request->is('post')
            ||  $this->request->is('put')
        ) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }

            $this->Session->setFlash(__('The user could not be saved. Please try again.'));
        } else {
            // existing user to be edited
            $this->request->data = $this->User->read(null, $id);
            $this->set('user', $this->User->read(null, $id));

            unset($this->request->data['User']['password']);
        }
    }

    /**
     * Action: Deletes a user
     *
     * @param   mixed $id       the id code of the object
     * @return  mixed  (see /lib/Cake/Controller/Controller.php)
     * @throws  NotFoundException if $id does not match an existing object
     */
    public function delete($id = null)
    {
        $this->request->onlyAllow('post');

        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        
        if ($this->User->delete()) {
            $this->Session->setFlash(__('User deleted'));
            return $this->redirect(array('action' => 'index'));
        }

        $this->Session->setFlash(__('User was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}