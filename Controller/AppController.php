<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file.
 */
App::uses('Controller', 'Controller');

// global values: imdbStatuses hardcoded db values
Configure::write('imdbStatuses.nodata',         0);
Configure::write('imdbStatuses.partialdata',    1);
Configure::write('imdbStatuses.fulldata',       2);
Configure::write('imdbStatuses.error',          3);

// global values: filmStatuses hardcoded db values
Configure::write('filmStatuses.notseen',        0);
Configure::write('filmStatuses.seen',           1);
Configure::write('filmStatuses.partial',        2);

// defaults / limits for stats images
Configure::write('statsImage.height',           400);
Configure::write('statsImage.width.years',      400);
Configure::write('statsImage.width.months',     1500);
Configure::write('statsImage.limit.years',      20);    // how many years to show in stats years graph
Configure::write('statsImage.limit.months',     110);   // same, but months for year/month graph


/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 */
class AppController extends Controller
{

    /**
     *  $components
     *
     *  CakePHP: load components for this app
     */
    public $components = array(
        
        // add debug toolbar
        'DebugKit.Toolbar',
        
        // add session
        'Session',
        
        // add automatic relogin
        'AutoLogin',
        
        // authentication
        'Auth' => array(
            'loginRedirect' => array('controller' => 'Films', 'action' => 'index'),
            'logoutRedirect' => array('controller' => 'Pages', 'action' => 'display', 'home'),
            'authorize' => array('Controller'),
        ),
        
        // add access control lists
        'Acl',

        
    );


    /**
     *  beforeFilter
     *
     *  CakePHP: This function is executed before every action in the controller
     */
    public function beforeFilter()
    {
        // this is no longer relevant, Acl-access is determined per object
        //$this->Auth->allow('index', 'view');
        
        // remember referer for redirection on login even if we're logging in
        // through a controller that doesn't (always) require authentication
        if (Router::url($this->Auth->loginAction) != $this->here) {
            $this->Session->write('Auth.redirect', $this->here);
        } 
    }


    /**
     *  isAuthorized
     *
     *  CakePHP: Check if user can access this part of the site
     */
    public function isAuthorized($user = null)
    {
        // check Acl: anyone with admin rights over Films should be allowed access to all
        if ($this->Acl->check($user['role'], 'Films', 'admin')) {
            return true;
        }

        // default deny
        return false;
    }
}
