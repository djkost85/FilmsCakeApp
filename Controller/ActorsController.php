<?php
/**
 * Actor controller for CakePHP.
 *
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    0.9.0
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */

App::uses('AppController', 'Controller');

/**
 * Actor controller (MVC component)
 *
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    Release: @package_version@
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */
class ActorsController extends AppController
{
    /**
     * CakePHP: $components
     *  
     * @var array
     */
    public $components = array('Paginator');


    /**
     * Action: search -- Show a list of actors, apply search filter
     *
     * @return void
     */
    public function search() {

        // set the page we will redirect to
        $url['action'] = 'index';
        
        // build a URL will all the search elements in it
        foreach ($this->data as $k => $v) {
            foreach ($v as $kk => $vv) {
                $url[$k . '.' . $kk] = $vv;
            }
        }

        $this->redirect($url, null, true);
    }


    /**
     * Action: index -- list of actors
     *
     * @return void
     */
    public function index()
    {
        $this->Actor->recursive = 0;

        // paginate per 50, default sort by film count
        $this->Paginator->settings = array(
            'order' => array(
                'Actor.actorrole_count' => 'desc',
            ),
            'limit' => 50,
        );

        // search conditions for filter form
        // name
        if (!empty($this->passedArgs['Search.name'])) {
            // if there are no wildcards, place entire string between wildcards
            // else, replace the * for %
            if (preg_match('/\*/', $this->passedArgs['Search.name'])) {
                $this->Paginator->settings['conditions'][]['Actor.name LIKE'] = str_replace('*', '%',
                    $this->passedArgs['Search.name']
                );
            } else {
                $this->Paginator->settings['conditions'][]['Actor.name LIKE'] = '%'
                                                                              . $this->passedArgs['Search.name']
                                                                              . '%';
            }
            $this->request->data['Search']['name'] = $this->passedArgs['Search.name'];
        }

        $this->set('actors', $this->paginate());
    }

    /**
     * Action: view -- show a single actor
     *
     * @param   mixed $id       object id (usually integer) for the item being viewed
     * @return  void
     * @throws  NotFoundException if id does not match any existing object
     */
    public function view($id = null)
    {
        $this->Actor->recursive = 0;

        if (!$this->Actor->exists($id)) {
            throw new NotFoundException(__('Invalid actor'));
        }

        // get actor data (not recursive, so just the actor)
        $this->set('actor', $this->Actor->find('first', array(
            'conditions' => array('Actor.' . $this->Actor->primaryKey => $id),
        )));

        // get roles the actor played
        // contain to avoid retrieving all Actor data again
        $this->Actor->ActorRole->Behaviors->attach('Containable');

        $options = array(
            'conditions' => array('ActorRole.actor_id' => $id),
            'contain' => array(
                'Film' => array('id','title', 'year','imdb','seen_last'),
            ),
            'order' => array(
                'Film.year' => 'desc',
                'Film.title' => 'asc',
            ),
        );

        $this->set('roles', $this->Actor->ActorRole->find('all', $options));
    }
}
