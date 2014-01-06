<?php
/**
 * Director controller for CakePHP.
 *
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    0.9.0
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */

App::uses('AppController', 'Controller');

/**
 * Director controller (MVC component)
 *
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    Release: @package_version@
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */
class DirectorsController extends AppController
{
	/**
     * CakePHP: $components
     *  
     * @var array
     */
    public $components = array('Paginator');


    /**
     * Action: search -- Show a list of directors, apply search filter
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
	 * Action: index -- list of directors
	 *
	 * @return void
	 */
	public function index()
	{
		$this->Director->recursive = 0;

        // paginate per 50, default sort by film count
        $this->Paginator->settings = array(
            'order' => array(
                'Director.film_count' => 'desc',
            ),
            'limit' => 50,
        );

        // search conditions for filter form
        // name
        if (!empty($this->passedArgs['Search.name'])) {
            // if there are no wildcards, place entire string between wildcards
            // else, replace the * for %
            if (preg_match('/\*/', $this->passedArgs['Search.name'])) {
                $this->Paginator->settings['conditions'][]['Director.name LIKE'] = str_replace('*', '%',
                    $this->passedArgs['Search.name']
                );
            } else {
                $this->Paginator->settings['conditions'][]['Director.name LIKE'] = '%'
                                                                              . $this->passedArgs['Search.name']
                                                                              . '%';
            }
            $this->request->data['Search']['name'] = $this->passedArgs['Search.name'];
        }

		$this->set('directors', $this->paginate());
	}


	/**
	 * Action: view -- show a single director
	 *
	 * @param   mixed $id       object id (usually integer) for the item being viewed
	 * @return 	void
	 * @throws 	NotFoundException if id does not match any existing object
	 */
	public function view($id = null)
	{
		$this->Director->recursive = 1;

		if (!$this->Director->exists($id)) {
			throw new NotFoundException(__('Invalid director'));
		}

		// contain to avoid unnecessary data loading
		$this->Director->Behaviors->attach('Containable');

		// get the director data & associated films
		$this->set('director', $this->Director->find('first', array(
			'conditions' => array('Director.' . $this->Director->primaryKey => $id),
			'contain' => array(
				'Film' => array('id','title', 'year','imdb','seen_last', 'grade'),
			),
		)));
	}

	/**
	 * Action: add -- add a new director manually
	 *
	 * @return 	void
	 * @throws 	NotFoundException if id does not match any existing object
	 */
	public function add()
	{
		if ($this->request->is('post')) {
			$this->Director->create();
			if ($this->Director->save($this->request->data)) {
				$this->Session->setFlash(__('The director has been saved'), 'default',
					array('class' => 'message success')
				);
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The director could not be saved. Please, try again.'), 'default',
					array('class' => 'message error')
				);
			}
		}
		$films = $this->Director->Film->find('list');
		$this->set(compact('films'));
	}

	/**
	 * Action: edit -- edit a single director
	 *
	 * @param   mixed $id       object id (usually integer) for the item being viewed
	 * @return 	void
	 * @throws 	NotFoundException if id does not match any existing object
	 */
	public function edit($id = null)
	{
		if (!$this->Director->exists($id)) {
			throw new NotFoundException(__('Invalid director'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Director->save($this->request->data)) {
				$this->Session->setFlash(__('The director has been saved'), 'default',
					array('class' => 'message success')
				);
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The director could not be saved. Please, try again.'), 'default',
					array('class' => 'message error')
				);
			}
		} else {
			$options = array('conditions' => array('Director.' . $this->Director->primaryKey => $id));
			$this->request->data = $this->Director->find('first', $options);
		}

		// get a list of films for the director
		$films = $this->Director->Film->find('list');
		$this->set(compact('films'));
	}

	/**
	 * Action: delete -- delete a single director
	 *
	 * @param   mixed $id       object id (usually integer) for the item being viewed
	 * @return 	void
	 * @throws 	NotFoundException 			if id does not match any existing object
	 * @throws 	MethodNotAllowedException 	if the user does not have access to this action
	 */
	public function delete($id = null)
	{
		$this->Director->id = $id;
		if (!$this->Director->exists()) {
			throw new NotFoundException(__('Invalid director'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Director->delete()) {
			$this->Session->setFlash(__('The director has been deleted.'), 'default',
				array('class' => 'message success')
			);
		} else {
			$this->Session->setFlash(__('The director could not be deleted. Please, try again.'), 'default',
				array('class' => 'message error')
			);
		}
		return $this->redirect(array('action' => 'index'));
	}
}
